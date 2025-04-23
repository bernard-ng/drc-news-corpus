<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Domain\Exception;

use App\SharedKernel\Domain\Exception\UserFacingError;

/**
 * Class UserNotConfirmed.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class AccountNotConfirmed extends \DomainException implements UserFacingError
{
    #[\Override]
    public function translationId(): string
    {
        return 'identity_and_access.exceptions.account_not_confirmed';
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
