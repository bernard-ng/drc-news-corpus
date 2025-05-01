<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\QueryHandler;

use App\Aggregator\Application\ReadModel\Statistics\SourcesStatisticsOverview;
use App\Aggregator\Application\UseCase\Query\GetSourcesStatisticsOverview;
use App\SharedKernel\Application\Messaging\QueryHandler;

/**
 * Interface GetSourcesStatisticsOverviewHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface GetSourcesStatisticsOverviewHandler extends QueryHandler
{
    public function __invoke(GetSourcesStatisticsOverview $query): SourcesStatisticsOverview;
}
