<?php

declare(strict_types=1);

namespace App\FeedManagement\Domain\Model\Filters;

use App\SharedKernel\Domain\Model\ValueObject\DateRange;
use App\SharedKernel\Domain\Model\ValueObject\SortDirection;

/**
 * Class ArticleFilters.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class ArticleFilters
{
    public function __construct(
        public ?string $search = null,
        public ?string $category = null,
        public ?DateRange $dateRange = null,
        public SortDirection $sortDirection = SortDirection::DESC,
    ) {
    }
}
