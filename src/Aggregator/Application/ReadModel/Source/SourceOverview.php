<?php

declare(strict_types=1);

namespace App\Aggregator\Application\ReadModel\Source;

use App\SharedKernel\Domain\DataTransfert\DataMapping;

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
        public bool $followed = false,
        public ?string $image = null
    ) {
    }

    public static function create(array $item): self
    {
        return new self(
            DataMapping::string($item, 'source_name'),
            DataMapping::string($item, 'source_url'),
            DataMapping::integer($item, 'articles_count'),
            DataMapping::string($item, 'source_crawled_at'),
            DataMapping::nullableString($item, 'source_display_name'),
            DataMapping::nullableString($item, 'updated_at'),
            DataMapping::integer($item, 'articles_metadata_available'),
            DataMapping::boolean($item, 'source_is_followed'),
            DataMapping::nullableString($item, 'source_image')
        );
    }
}
