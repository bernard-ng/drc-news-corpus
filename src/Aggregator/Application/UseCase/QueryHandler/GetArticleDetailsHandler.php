<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\QueryHandler;

use App\Aggregator\Application\ReadModel\Article;
use App\Aggregator\Application\UseCase\Query\GetArticleDetailsQuery;
use App\SharedKernel\Application\Bus\QueryHandler;

/**
 * Class GetArticleDetailsDbalHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface GetArticleDetailsHandler extends QueryHandler
{
    public function __invoke(GetArticleDetailsQuery $query): Article;
}
