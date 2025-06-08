<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Model\Pagination;

/**
 * Class PaginatorKeyset.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class PaginatorKeyset
{
    /**
     * @param non-empty-string $id
     * @param non-empty-string|null $date
     */
    public function __construct(
        public string $id,
        public ?string $date = null,
    ) {
    }
}
