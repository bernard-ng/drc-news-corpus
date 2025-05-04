<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\UseCase\Command;

use App\IdentityAndAccess\Domain\Model\Identity\UserId;
use App\SharedKernel\Domain\Model\ValueObject\Tracking\ClientProfile;

/**
 * Class RegisterLogin.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class RegisterLoginSuccess
{
    public function __construct(
        public UserId $userId,
        public ClientProfile $profile
    ) {
    }
}
