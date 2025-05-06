<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\QueryHandler;

use App\Aggregator\Application\ReadModel\ArticleOverviewList;
use App\Aggregator\Application\UseCase\Query\GetArticleOverviewList;
use App\SharedKernel\Application\Messaging\QueryHandler;

/**
 * Class GetArticleOverviewListHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface GetArticleOverviewListHandler extends QueryHandler
{
    public function __invoke(GetArticleOverviewList $query): ArticleOverviewList;
}
