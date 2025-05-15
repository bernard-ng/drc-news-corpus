<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\UseCase\Command;

use App\Aggregator\Domain\Model\Identity\ArticleId;
use App\FeedManagement\Domain\Model\Identity\BookmarkId;
use App\IdentityAndAccess\Domain\Model\Identity\UserId;

/**
 * Class RemoveArticleFromBookmark.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class RemoveArticleFromBookmark
{
    public function __construct(
        public UserId $userId,
        public ArticleId $articleId,
        public BookmarkId $bookmarkId,
    ) {
    }
}
