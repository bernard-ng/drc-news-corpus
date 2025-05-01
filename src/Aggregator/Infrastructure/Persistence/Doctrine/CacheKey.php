<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine;

/**
 * Enum CacheKey.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
enum CacheKey: string
{
    case SOURCES_STATISTICS_OVERVIEW = 'statistics';
    case SOURCE_PUBLICATION_GRAPH = 'source_publication_graph_%s';
    case SOURCE_OVERVIEW = 'source_overview_%s';
    case SOURCE_CATEGORIES_SHARES = 'source_categories_shares_%s';
    case ARTICLE_DETAILS = 'article_details_%s';

    public function withId(string $id): string
    {
        return sprintf($this->value, $id);
    }
}
