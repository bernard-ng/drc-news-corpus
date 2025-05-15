<?php

declare(strict_types=1);

namespace App\FeedManagement\Domain\Exception;

use App\FeedManagement\Domain\Model\Identity\BookmarkId;
use App\SharedKernel\Domain\Exception\UserFacingError;

/**
 * Class BookmarkNotFound.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class BookmarkNotFound extends \DomainException implements UserFacingError
{
    public static function withId(BookmarkId $id): self
    {
        return new self(sprintf('Bookmark with id "%s" not found.', $id->toString()));
    }

    public function translationId(): string
    {
        return 'feed_management.exceptions.bookmark_not_found';
    }

    public function translationParameters(): array
    {
        return [];
    }

    public function translationDomain(): string
    {
        return 'feed_management';
    }
}
