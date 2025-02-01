<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain;

use App\SharedKernel\Domain\Exception\InvalidArgument;

/**
 * Class Assert.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Assert extends \Webmozart\Assert\Assert
{
    #[\Override]
    protected static function reportInvalidArgument($message): void
    {
        throw new InvalidArgument($message);
    }
}
