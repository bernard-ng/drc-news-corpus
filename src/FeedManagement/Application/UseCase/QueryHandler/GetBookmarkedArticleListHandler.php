<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\UseCase\QueryHandler;

use App\FeedManagement\Application\ReadModel\ArticleOverviewList;
use App\FeedManagement\Application\UseCase\Query\GetBookmarkedArticleList;
use App\SharedKernel\Application\Messaging\QueryHandler;

/**
 * Interface GetBookmarkedArticleListHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface GetBookmarkedArticleListHandler extends QueryHandler
{
    public function __invoke(GetBookmarkedArticleList $query): ArticleOverviewList;
}
