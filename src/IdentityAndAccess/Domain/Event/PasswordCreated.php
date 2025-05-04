<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Domain\Event;

use App\IdentityAndAccess\Domain\Model\Identity\UserId;
use App\IdentityAndAccess\Domain\Model\ValueObject\Secret\GeneratedCode;

/**
 * Class PasswordCreated.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class PasswordCreated
{
    public function __construct(
        public UserId $userId,
        #[\SensitiveParameter] public GeneratedCode $password
    ) {
    }
}
