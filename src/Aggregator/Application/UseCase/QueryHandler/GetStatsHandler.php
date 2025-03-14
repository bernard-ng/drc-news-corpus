<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\QueryHandler;

use App\Aggregator\Application\ReadModel\Statistics;
use App\Aggregator\Application\UseCase\Query\GetStatsQuery;
use App\SharedKernel\Application\Bus\QueryHandler;

/**
 * Interface GetStatsHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface GetStatsHandler extends QueryHandler
{
    public function __invoke(GetStatsQuery $query): Statistics;
}
