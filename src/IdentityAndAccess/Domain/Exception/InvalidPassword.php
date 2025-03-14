<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Domain\Exception;

use App\SharedKernel\Domain\Exception\UserFacingError;

/**
 * Class InvalidPassword.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class InvalidPassword extends \DomainException implements UserFacingError
{
    #[\Override]
    public function translationId(): string
    {
        return 'identity_and_access.exceptions.invalid_current_password';
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
