<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\UseCase\Command;

use App\IdentityAndAccess\Domain\Model\ValueObject\Roles;
use App\SharedKernel\Domain\Model\ValueObject\EmailAddress;

/**
 * Class Register.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class Register
{
    public function __construct(
        public string $name,
        public EmailAddress $email,
        public ?string $password,
        public Roles $roles = new Roles()
    ) {
    }
}
