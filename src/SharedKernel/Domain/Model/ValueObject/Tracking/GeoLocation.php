<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Model\ValueObject\Tracking;

use App\SharedKernel\Domain\Assert;

/**
 * Class GeoLocation.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GeoLocation
{
    public function __construct(
        public ?string $country = null,
        public ?string $city = null,
        public ?string $timeZone = null,
        public ?float $longitude = null,
        public ?float $latitude = null,
        public ?int $accuracyRadius = null,
    ) {
    }

    public static function from(array $data): self
    {
        Assert::keyExists($data, 'country');
        Assert::keyExists($data, 'city');
        Assert::keyExists($data, 'time_zone');
        Assert::keyExists($data, 'longitude');
        Assert::keyExists($data, 'latitude');
        Assert::keyExists($data, 'accuracy_radius');

        return new self(
            country: $data['country'] ?? null,
            city: $data['city'] ?? null,
            timeZone: $data['time_zone'] ?? null,
            longitude: $data['longitude'] ?? null,
            latitude: $data['latitude'] ?? null,
            accuracyRadius: $data['accuracy_radius'] ?? null,
        );
    }

    public static function empty(): self
    {
        return new self();
    }
}
