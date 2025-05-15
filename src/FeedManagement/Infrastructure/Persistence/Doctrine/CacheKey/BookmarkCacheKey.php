<?php

declare(strict_types=1);

namespace App\FeedManagement\Infrastructure\Persistence\Doctrine\CacheKey;

/**
 * Class BookmarkCacheKey.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
enum BookmarkCacheKey: string
{
    case BOOKMARK_INFO = 'bookmark_info_%s';

    case BOOKMARK_INFO_LIST = 'bookmark_info_list_%s';

    case BOOKMARKED_ARTICLE_LIST = 'bookmarked_article_list_%s';

    public function withId(string $id): string
    {
        return sprintf($this->value, $id);
    }
}
