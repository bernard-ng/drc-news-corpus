<?php

declare(strict_types=1);

namespace App\Aggregator\Application\ReadModel\Source;

/**
 * Class SourceOverview.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class SourceOverview
{
    public function __construct(
        public string $name,
        public string $url,
        public int $articlesCount,
        public string $crawledAt,
        public ?string $displayName = null,
        public ?string $updatedAt = null,
        public int $metadataAvailable = 0,
        public bool $followed = false
    ) {
    }
}
