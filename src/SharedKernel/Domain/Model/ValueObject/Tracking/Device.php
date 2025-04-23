<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Model\ValueObject\Tracking;

/**
 * Class Device.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class Device
{
    public function __construct(
        public ?string $operatingSystem = null,
        public ?string $client = null,
        public ?string $device = null,
        public bool $isBot = false,
    ) {
    }

    public static function empty(): self
    {
        return new self();
    }
}
