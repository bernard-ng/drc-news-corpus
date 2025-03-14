<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Domain\Model\Event;

use App\IdentityAndAccess\Domain\Model\Entity\Identity\UserId;

/**
 * Class PasswordUpdated.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class PasswordUpdated
{
    public function __construct(
        public UserId $id
    ) {
    }
}
