<?php

declare(strict_types=1);

namespace App\Aggregator\Application\ReadModel;

/**
 * Class SourceStatistics.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class SourceStatistics
{
    public function __construct(
        public int $total,
        public string $source,
        public string $lastCrawledAt,
    ) {
    }
}
