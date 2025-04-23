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
    case STATISTICS = 'statistics';
}
