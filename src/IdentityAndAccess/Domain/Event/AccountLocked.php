<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Domain\Event;

use App\IdentityAndAccess\Domain\Model\Identity\UserId;
use App\IdentityAndAccess\Domain\Model\ValueObject\Secret\GeneratedToken;

/**
 * Class AccountLocked.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class AccountLocked
{
    public function __construct(
        public UserId $userId,
        public GeneratedToken $token
    ) {
    }
}
