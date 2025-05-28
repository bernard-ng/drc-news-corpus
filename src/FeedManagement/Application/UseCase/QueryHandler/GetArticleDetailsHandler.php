<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\UseCase\QueryHandler;

use App\FeedManagement\Application\ReadModel\ArticleDetails;
use App\FeedManagement\Application\UseCase\Query\GetArticleDetails;
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
