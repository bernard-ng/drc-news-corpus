<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\UseCase\QueryHandler;

use App\FeedManagement\Application\ReadModel\ArticleOverviewList;
use App\FeedManagement\Application\UseCase\Query\GetArticleOverviewList;
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
