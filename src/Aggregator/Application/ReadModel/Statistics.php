<?php

declare(strict_types=1);

namespace App\Aggregator\Application\ReadModel;

use App\SharedKernel\Domain\Assert;

/**
 * Class Statistics.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class Statistics
{
    public function __construct(
        public array $items
    ) {
        Assert::allIsInstanceOf($items, SourceStatistics::class);
    }
}
