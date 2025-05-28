<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\UseCase\QueryHandler;

use App\FeedManagement\Application\ReadModel\BookmarkList;
use App\FeedManagement\Application\UseCase\Query\GetBookmarkList;
use App\SharedKernel\Application\Messaging\QueryHandler;

/**
 * Interface GetBookmarkListHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface GetBookmarkListHandler extends QueryHandler
{
    public function __invoke(GetBookmarkList $query): BookmarkList;
}
