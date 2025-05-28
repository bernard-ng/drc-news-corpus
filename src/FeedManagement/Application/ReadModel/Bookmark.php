<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\ReadModel;

use App\FeedManagement\Domain\Model\Identity\BookmarkId;
use App\SharedKernel\Domain\DataTransfert\DataMapping;

/**
 * Class Bookmark.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class Bookmark
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

    public static function create(array $item): self
    {
        return new self(
            BookmarkId::fromBinary($item['bookmark_id']),
            DataMapping::string($item, 'bookmark_name'),
            DataMapping::datetime($item, 'bookmark_created_at'),
            DataMapping::nullableString($item, 'bookmark_description'),
            DataMapping::integer($item, 'bookmark_articles_count'),
            DataMapping::boolean($item, 'bookmark_is_public'),
            DataMapping::nullableDatetime($item, 'bookmark_updated_at')
        );
    }
}
