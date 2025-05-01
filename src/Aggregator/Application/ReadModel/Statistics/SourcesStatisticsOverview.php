<?php

declare(strict_types=1);

namespace App\Aggregator\Application\ReadModel\Statistics;

use App\SharedKernel\Domain\Assert;

/**
 * Class SourcesStatisticsOverview.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class SourcesStatisticsOverview
{
    public function __construct(
        public array $items = []
    ) {
        Assert::allIsInstanceOf($items, SourceOverview::class);
    }
}
