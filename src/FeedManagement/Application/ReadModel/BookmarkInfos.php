<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\ReadModel;

use App\FeedManagement\Domain\Model\Identity\BookmarkId;

/**
 * Class BookmarkInfos.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class BookmarkInfos
{
    public function __construct(
        public BookmarkId $id,
        public string $name,
        public \DateTimeImmutable $createdAt,
        public ?string $description = null,
        public int $articlesCount = 0,
        public bool $isPublic = false,
        public ?\DateTimeImmutable $updatedAt = null
    ) {
    }
}
