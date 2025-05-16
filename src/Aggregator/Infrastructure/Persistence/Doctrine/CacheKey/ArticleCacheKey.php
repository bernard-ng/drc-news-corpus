<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\CacheKey;

/**
 * Enum ArticleCacheKey.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
enum ArticleCacheKey: string
{
    case ARTICLE_DETAILS = 'article_details_%s';

    public function withId(string $id): string
    {
        return sprintf($this->value, $id);
    }
}
