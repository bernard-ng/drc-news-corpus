<?php

declare(strict_types=1);

namespace App\FeedManagement\Domain\Exception;

use App\Aggregator\Domain\Model\Identity\ArticleId;
use App\FeedManagement\Domain\Model\Identity\BookmarkId;
use App\SharedKernel\Domain\Exception\UserFacingError;

/**
 * Class ArticleAlreadyBookmarked.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class ArticleAlreadyBookmarked extends \DomainException implements UserFacingError
{
    public static function with(ArticleId $articleId, BookmarkId $bookmarkId): self
    {
        return new self(sprintf('Article %s already bookmarked in bookmark %s', $articleId->toString(), $bookmarkId->toString()));
    }

    public function translationId(): string
    {
        return 'feed_management.exceptions.article_already_bookmarked';
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
