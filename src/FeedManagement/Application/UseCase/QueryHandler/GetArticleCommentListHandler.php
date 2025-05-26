<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\UseCase\QueryHandler;

use App\FeedManagement\Application\ReadModel\CommentList;
use App\FeedManagement\Application\UseCase\Query\GetArticleCommentList;
use App\SharedKernel\Application\Messaging\QueryHandler;

/**
 * Class GetArticleCommentListHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface GetArticleCommentListHandler extends QueryHandler
{
    public function __invoke(GetArticleCommentList $query): CommentList;
}
