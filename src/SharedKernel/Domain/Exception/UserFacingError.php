<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Exception;

/**
 * Interface UserFacingError.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface UserFacingError extends \Throwable
{
    public function translationId(): string;

    public function translationParameters(): array;

    public function translationDomain(): string;
}
