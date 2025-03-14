<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Domain\Model\Event;

use App\IdentityAndAccess\Domain\Model\Entity\Identity\UserId;
use App\IdentityAndAccess\Domain\Model\ValueObject\Secret\TimedToken;

/**
 * Class PasswordForgotten.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class PasswordForgotten
{
    public function __construct(
        public UserId $id,
        public TimedToken $token,
    ) {
    }
}
