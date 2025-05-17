<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Persistence\Filesystem\Asset;

use App\SharedKernel\Application\Asset\AssetType;
use App\SharedKernel\Application\Asset\AssetUrlProvider as AssetUrlProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Class AssetUrlProvider.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class AssetUrlProvider implements AssetUrlProviderInterface
{
    public function __construct(
        #[Autowire(env: 'SERVER_ADDR')] public ?string $serverAddress = null,
        #[Autowire(env: 'SERVER_PORT')] public ?string $serverPort = null,
        #[Autowire(env: 'APP_ENV')] public string $env = 'dev'
    ) {
        $this->serverAddress ??= $_SERVER['SERVER_ADDR'] ?? null;
        $this->serverPort ??= $_SERVER['SERVER_PORT'] ?? null;
    }

    public function getUrl(string $id, AssetType $type): ?string
    {
        if ($this->serverAddress === null) {
            return null;
        }

        $path = match ($type) {
            AssetType::SOURCE_PROFILE_IMAGE => sprintf('/images/sources/%s.png', $id),
        };

        $scheme = $this->env === 'prod' ? 'https' : 'http';
        return sprintf('%s://%s:%s/%s', $scheme, $this->serverAddress, $this->serverPort, $path);
    }
}
