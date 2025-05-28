<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\Cache;

/**
 * Enum SourceCacheAttributes.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
enum SourceCacheAttributes: string
{
    case CATEGORIES = 'categories_shares';

    case PUBLICATIONS = 'publications_graph';

    public const int CACHE_TTL = 86400;

    public function withId(string $id): string
    {
        return sprintf('%s_%s', $this->value, $id);
    }
}
