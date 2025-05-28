<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\QueryHandler;

use App\Aggregator\Application\ReadModel\SourceStatisticsList;
use App\Aggregator\Application\UseCase\Query\GetSourceStatisticsList;
use App\SharedKernel\Application\Messaging\QueryHandler;

/**
 * Interface GetSourceStatisticsListHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface GetSourceStatisticsListHandler extends QueryHandler
{
    public function __invoke(GetSourceStatisticsList $query): SourceStatisticsList;
}
