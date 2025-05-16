<?php

declare(strict_types=1);

namespace App\Aggregator\Application\ReadModel\Source;

/**
 * Class DallyEntry.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class DailyEntry
{
    public function __construct(
        public string $date,
        public int $count
    ) {
    }
}
