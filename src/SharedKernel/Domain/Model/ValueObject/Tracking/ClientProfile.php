<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Model\ValueObject\Tracking;

/**
 * Class ClientProfile.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class ClientProfile
{
    public function __construct(
        #[\SensitiveParameter] public ?string $userIp = null,
        public ?string $userAgent = null,
        public array $hints = []
    ) {
    }
}
