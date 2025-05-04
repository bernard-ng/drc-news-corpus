<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Tracking;

use App\SharedKernel\Domain\Assert;
use App\SharedKernel\Domain\Model\ValueObject\Tracking\ClientProfile;
use App\SharedKernel\Domain\Model\ValueObject\Tracking\Device;
use App\SharedKernel\Domain\Model\ValueObject\Tracking\GeoLocation;
use App\SharedKernel\Domain\Tracking\ClientProfiler as ClientProfilerInterface;
use DeviceDetector\ClientHints;
use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Client\Browser;
use DeviceDetector\Parser\OperatingSystem;
use GeoIp2\Database\Reader;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\IpUtils;

/**
 * Class ClientProfiler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class ClientProfiler implements ClientProfilerInterface
{
    private const string GEOIP_CITY_DATABASE = 'geoip_city.mmdb';

    private const string GEOIP_COUNTRY_DATABASE = 'geoip_country.mmdb';

    public function __construct(
        private string $projectDir,
        private LoggerInterface $logger
    ) {
    }

    #[\Override]
    public function locate(ClientProfile $profile): GeoLocation
    {
        if ($this->shouldSkipIpLocalization($profile)) {
            return GeoLocation::empty();
        }

        try {
            $database = sprintf('%s/%s', $this->projectDir, self::GEOIP_CITY_DATABASE);

            Assert::notNull($profile->userIp);
            $data = new Reader($database)->city($profile->userIp);

            return GeoLocation::from([
                'country' => $data->country->isoCode,
                'city' => $data->city->name,
                'time_zone' => $data->location->timeZone,
                'longitude' => $data->location->longitude,
                'latitude' => $data->location->latitude,
                'accuracy_radius' => $data->location->accuracyRadius,
            ]);
        } catch (\Throwable $e) {
            $this->logger->error('Unable to fetch location from IP address', [
                'ip' => $profile->userIp,
                'exception' => $e,
            ]);

            return GeoLocation::empty();
        }
    }

    #[\Override]
    public function locateCountry(ClientProfile $profile): ?string
    {
        if ($this->shouldSkipIpLocalization($profile)) {
            return null;
        }

        try {
            $database = sprintf('%s/%s', $this->projectDir, self::GEOIP_COUNTRY_DATABASE);

            Assert::notNull($profile->userIp);
            $data = new Reader($database)->country($profile->userIp);

            /** @var string|null $country */
            $country = $data->country->isoCode;

            return $country;
        } catch (\Throwable $e) {
            $this->logger->error('Unable to fetch country from IP address', [
                'ip' => $profile->userIp,
                'exception' => $e,
            ]);

            return null;
        }
    }

    #[\Override]
    public function detect(ClientProfile $profile): Device
    {
        if ($profile->userAgent === null || $profile->hints === []) {
            return Device::empty();
        }

        try {
            $detector = new DeviceDetector($profile->userAgent, ClientHints::factory($profile->hints));
            $detector->parse();

            $osLabel = is_string($detector->getOs('name')) ? $detector->getOs('name') : '';
            $clientLabel = is_string($detector->getClient('name')) ? $detector->getClient('name') : '';

            return new Device(
                operatingSystem: OperatingSystem::getOsFamily($osLabel),
                client: match (true) {
                    $detector->isBrowser() => Browser::getBrowserFamily($clientLabel),
                    default => $clientLabel
                },
                device: $detector->getDeviceName(),
                isBot: $detector->isBot(),
            );
        } catch (\Throwable $e) {
            $this->logger->error('Unable to detect device', [
                'user_agent' => $profile->userAgent,
                'exception' => $e,
            ]);

            return Device::empty();
        }
    }

    private function shouldSkipIpLocalization(ClientProfile $profile): bool
    {
        return \filter_var($profile->userIp, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV6)
            || $profile->userIp === null
            || IpUtils::isPrivateIp($profile->userIp);
    }
}
