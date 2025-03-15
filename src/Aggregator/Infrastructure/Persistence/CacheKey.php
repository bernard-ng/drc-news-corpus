<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence;

/**
 * Enum CacheKey.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
enum CacheKey: string
{
    case STATISTICS = 'statistics';
}
