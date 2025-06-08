<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\UseCase\Query;

use App\FeedManagement\Domain\Model\Filters\ArticleFilters;
use App\FeedManagement\Domain\Model\Identity\BookmarkId;
use App\IdentityAndAccess\Domain\Model\Identity\UserId;
use App\SharedKernel\Domain\Model\Pagination\Page;

/**
 * Class GetBookmarkedArticleList.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetBookmarkedArticleList
{
    public function __construct(
        public UserId $userId,
        public BookmarkId $bookmarkId,
        public Page $page = new Page(),
        public ArticleFilters $filters = new ArticleFilters()
    ) {
    }
}
