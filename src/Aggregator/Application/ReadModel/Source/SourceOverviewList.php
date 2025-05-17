<?php

declare(strict_types=1);

namespace App\Aggregator\Application\ReadModel\Source;

use App\SharedKernel\Domain\Assert;
use App\SharedKernel\Domain\Model\ValueObject\Pagination;

/**
 * Class SourceOverviewList.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class SourceOverviewList
{
    public function __construct(
        public array $items,
        public Pagination $pagination,
    ) {
        Assert::allIsInstanceOf($items, SourceOverview::class);
    }
}
