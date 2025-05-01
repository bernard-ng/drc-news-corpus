<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Model\ValueObject\Filters;

use App\Aggregator\Domain\Model\ValueObject\Crawling\DateRange;

/**
 * Class ArticleFilters.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class ArticleFilters
{
    public function __construct(
        public ?string $search = null,
        public ?string $source = null,
        public ?string $category = null,
        public ?DateRange $dateRange = null,
    ) {
    }
}
