<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\QueryHandler;

use App\Aggregator\Application\ReadModel\Statistics\SourceStatisticsDetails;
use App\Aggregator\Application\UseCase\Query\GetSourceStatisticsDetails;
use App\SharedKernel\Application\Bus\QueryHandler;

/**
 * Interface GetSourceStatisticsDetailsHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface GetSourceStatisticsDetailsHandler extends QueryHandler
{
    public function __invoke(GetSourceStatisticsDetails $query): SourceStatisticsDetails;
}
