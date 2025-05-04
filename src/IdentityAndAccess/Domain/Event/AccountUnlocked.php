<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Domain\Event;

use App\IdentityAndAccess\Domain\Model\Identity\UserId;

/**
 * Class AccountUnlocked.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class AccountUnlocked
{
    public function __construct(
        public UserId $userId
    ) {
    }
}
