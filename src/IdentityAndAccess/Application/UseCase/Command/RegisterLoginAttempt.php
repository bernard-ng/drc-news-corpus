<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\UseCase\Command;

use App\IdentityAndAccess\Domain\Model\Identity\UserId;

/**
 * Class AddLoginAttempt.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class RegisterLoginAttempt
{
    public function __construct(
        public UserId $userId
    ) {
    }
}
