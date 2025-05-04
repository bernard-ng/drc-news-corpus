<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Domain\Event;

use App\IdentityAndAccess\Domain\Model\Identity\UserId;
use App\SharedKernel\Domain\Model\ValueObject\Tracking\Device;
use App\SharedKernel\Domain\Model\ValueObject\Tracking\GeoLocation;

/**
 * Class LoginProfileChanged.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class LoginProfileChanged
{
    public function __construct(
        public UserId $userId,
        public Device $device,
        public GeoLocation $location
    ) {
    }
}
