<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\UseCase\Command;

use App\SharedKernel\Domain\Model\ValueObject\Email;

/**
 * Class RequestPasswordReset.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class RequestPasswordReset
{
    public function __construct(
        public Email $email
    ) {
    }
}
