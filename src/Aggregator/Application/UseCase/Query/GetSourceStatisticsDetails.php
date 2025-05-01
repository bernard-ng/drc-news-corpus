<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\Query;

/**
 * Class GetSourceStatisticsDetails.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetSourceStatisticsDetails
{
    public function __construct(
        public string $source
    ) {
    }
}
