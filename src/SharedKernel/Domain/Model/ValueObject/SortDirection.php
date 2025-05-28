<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Model\ValueObject;

/**
 * Enum SortDirection.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
enum SortDirection: string
{
    case ASC = 'asc';
    case DESC = 'desc';

    public function opposite(): self
    {
        return match ($this) {
            self::ASC => self::DESC,
            self::DESC => self::ASC,
        };
    }
}
