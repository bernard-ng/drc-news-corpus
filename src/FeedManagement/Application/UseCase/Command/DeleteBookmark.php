<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\UseCase\Command;

use App\FeedManagement\Domain\Model\Identity\BookmarkId;
use App\IdentityAndAccess\Domain\Model\Identity\UserId;

/**
 * Class DeleteBookmark.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class DeleteBookmark
{
    public function __construct(
        public UserId $userId,
        public BookmarkId $bookmarkId
    ) {
    }
}
