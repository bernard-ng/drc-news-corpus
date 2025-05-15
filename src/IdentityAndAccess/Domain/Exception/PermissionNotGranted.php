<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Domain\Exception;

use App\SharedKernel\Domain\Exception\UserFacingError;

/**
 * Class PermissionNotGranted.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class PermissionNotGranted extends \DomainException implements UserFacingError
{
    public static function withReason(string $message): self
    {
        return new self($message);
    }

    public function translationId(): string
    {
        return 'identity_and_access.exceptions.permission_not_granted';
    }

    public function translationParameters(): array
    {
        return [
            '{reason}' => $this->message,
        ];
    }

    public function translationDomain(): string
    {
        return 'identity_and_access';
    }
}
