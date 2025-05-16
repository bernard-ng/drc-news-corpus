<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\QueryHandler;

use App\Aggregator\Application\ReadModel\Source\SourceOverviewList;
use App\Aggregator\Application\UseCase\Query\GetSourceOverviewList;
use App\SharedKernel\Application\Messaging\QueryHandler;

/**
 * Interface GetSourceOverviewListHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface GetSourceOverviewListHandler extends QueryHandler
{
    public function __invoke(GetSourceOverviewList $query): SourceOverviewList;
}
