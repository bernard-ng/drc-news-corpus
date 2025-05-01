<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\QueryHandler;

use App\Aggregator\Application\ReadModel\ArticleDetails;
use App\Aggregator\Application\UseCase\Query\GetArticleDetails;
use App\SharedKernel\Application\Messaging\QueryHandler;

/**
 * Class GetArticleDetailsDbalHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface GetArticleDetailsHandler extends QueryHandler
{
    public function __invoke(GetArticleDetails $query): ArticleDetails;
}
