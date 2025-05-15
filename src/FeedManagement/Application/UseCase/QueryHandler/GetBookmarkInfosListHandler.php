<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\UseCase\QueryHandler;

use App\FeedManagement\Application\ReadModel\BookmarkInfosList;
use App\FeedManagement\Application\UseCase\Query\GetBookmarkInfosList;
use App\SharedKernel\Application\Messaging\QueryHandler;

/**
 * Interface GetBookmarkInfosListHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface GetBookmarkInfosListHandler extends QueryHandler
{
    public function __invoke(GetBookmarkInfosList $query): BookmarkInfosList;
}
