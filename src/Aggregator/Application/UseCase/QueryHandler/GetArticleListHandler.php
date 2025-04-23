<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\QueryHandler;

use App\Aggregator\Application\ReadModel\ArticleList;
use App\Aggregator\Application\UseCase\Query\GetArticleList;
use App\SharedKernel\Application\Bus\QueryHandler;

/**
 * Class GetArticleListHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface GetArticleListHandler extends QueryHandler
{
    public function __invoke(GetArticleList $query): ArticleList;
}
