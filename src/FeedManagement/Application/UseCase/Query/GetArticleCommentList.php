<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\UseCase\Query;

use App\Aggregator\Domain\Model\Identity\ArticleId;
use App\SharedKernel\Domain\Model\Pagination\Page;

/**
 * Class GetArticleCommentListHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetArticleCommentList
{
    public function __construct(
        public ArticleId $articleId,
        public Page $page = new Page(),
    ) {
    }
}
