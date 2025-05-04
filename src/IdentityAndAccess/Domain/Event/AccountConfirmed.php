<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Domain\Event;

use App\IdentityAndAccess\Domain\Model\Identity\UserId;

/**
 * Class UserConfirmed.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class AccountConfirmed
{
    public function __construct(
        public UserId $userId
    ) {
    }
}
