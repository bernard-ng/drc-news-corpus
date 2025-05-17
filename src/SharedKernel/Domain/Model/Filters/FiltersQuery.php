<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Model\Filters;

use App\Aggregator\Domain\Model\ValueObject\Crawling\DateRange;

/**
 * Class FiltersQuery.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class FiltersQuery
{
    public function __construct(
        public ?string $search = null,
        public ?string $source = null,
        public ?string $category = null,
        public ?DateRange $dateRange = null,
    ) {
    }
}
