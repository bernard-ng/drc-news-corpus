<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Domain\Exception;

use App\SharedKernel\Domain\Exception\UserFacingError;

/**
 * Class TooManyLoginAttempts.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class AccountIsLocked extends \DomainException implements UserFacingError
{
    #[\Override]
    public function translationId(): string
    {
        return 'identity_and_access.exceptions.account_is_locked';
    }

    #[\Override]
    public function translationParameters(): array
    {
        return [];
    }

    #[\Override]
    public function translationDomain(): string
    {
        return 'identity_and_access';
    }
}
