<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\UseCase\QueryHandler;

use App\FeedManagement\Application\ReadModel\SourceOverviewList;
use App\FeedManagement\Application\UseCase\Query\GetSourceOverviewList;
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
