<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\CacheKey;

/**
 * Enum SourceCacheKey.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
enum SourceCacheKey: string
{
    case SOURCE_OVERVIEW_LIST = 'source_overview_list';
    case SOURCE_PUBLICATION_GRAPH = 'source_publication_graph_%s';
    case SOURCE_OVERVIEW = 'source_overview_%s';

    case SOURCE_DETAILS = 'source_details_%s';
    case SOURCE_CATEGORY_SHARES = 'source_category_shares_%s';

    public function withId(string $id): string
    {
        return sprintf($this->value, $id);
    }
}
