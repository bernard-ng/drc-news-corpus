<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Model\Pagination;

/**
 * Class PaginationInfo.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class PaginationInfo
{
    public function __construct(
        public readonly int $current,
        public readonly int $limit,
        public ?string $cursor = null,
        public bool $hasNext = false,
    ) {
    }

    public static function from(Page $page): self
    {
        return new self($page->page, $page->limit);
    }
}
