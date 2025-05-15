<?php

declare(strict_types=1);

namespace App\FeedManagement\Domain\Model\Repository;

use App\FeedManagement\Domain\Model\Entity\Bookmark;
use App\FeedManagement\Domain\Model\Identity\BookmarkId;

/**
 * Interface BookmarkRepository.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface BookmarkRepository
{
    public function add(Bookmark $bookmark): void;

    public function remove(Bookmark $bookmark): void;

    public function getById(BookmarkId $bookmarkId): Bookmark;
}
