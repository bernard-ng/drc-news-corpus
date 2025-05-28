<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\UseCase\QueryHandler;

use App\FeedManagement\Application\ReadModel\SourceDetails;
use App\FeedManagement\Application\UseCase\Query\GetSourceDetails;
use App\SharedKernel\Application\Messaging\QueryHandler;

/**
 * Interface GetSourceDetailsHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface GetSourceDetailsHandler extends QueryHandler
{
    public function __invoke(GetSourceDetails $query): SourceDetails;
}
