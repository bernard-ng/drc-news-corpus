<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\UseCase\Command;

use App\IdentityAndAccess\Domain\Model\ValueObject\Secret\GeneratedToken;

/**
 * Class UnlockAccount.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class UnlockAccount
{
    public function __construct(
        public GeneratedToken $token,
    ) {
    }
}
