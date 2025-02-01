<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Exception;

/**
 * Class InvalidArgument.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class InvalidArgument extends \RuntimeException implements UserFacingError
{
    #[\Override]
    public function translationId(): string
    {
        return 'shared_kernel.exceptions.invalid_argument';
    }

    #[\Override]
    public function translationParameters(): array
    {
        return [];
    }

    #[\Override]
    public function translationDomain(): string
    {
        return 'messages';
    }
}
