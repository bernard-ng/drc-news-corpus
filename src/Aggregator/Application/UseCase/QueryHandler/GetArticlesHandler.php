<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\QueryHandler;

use App\Aggregator\Application\ReadModel\Articles;
use App\Aggregator\Application\UseCase\Query\GetArticlesQuery;
use App\SharedKernel\Application\Bus\QueryHandler;

/**
 * Class GetArticlesHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface GetArticlesHandler extends QueryHandler
{
    public function __invoke(GetArticlesQuery $query): Articles;
}
