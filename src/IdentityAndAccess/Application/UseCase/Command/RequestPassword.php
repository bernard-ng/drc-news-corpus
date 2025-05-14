<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\UseCase\Command;

use App\SharedKernel\Domain\Model\ValueObject\EmailAddress;

/**
 * Class RequestPassword.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class RequestPassword
{
    public function __construct(
        public EmailAddress $email
    ) {
    }
}
