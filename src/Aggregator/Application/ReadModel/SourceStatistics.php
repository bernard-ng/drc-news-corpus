<?php

declare(strict_types=1);

namespace App\Aggregator\Application\ReadModel;

use App\Aggregator\Domain\Model\Identity\SourceId;
use App\SharedKernel\Domain\DataTransfert\DataMapping;

/**
 * Class SourceStatistics.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class SourceStatistics
{
    public function __construct(
        public SourceId $id,
        public string $name,
        public int $articlesCount,
        public int $metadataAvailable,
        public ?\DateTimeImmutable $crawledAt = null
    ) {
    }

    public static function create(array $item): self
    {
        return new self(
            SourceId::fromBinary($item['source_id']),
            DataMapping::string($item, 'source_name'),
            DataMapping::integer($item, 'articles_count'),
            DataMapping::integer($item, 'article_metadata_available'),
            DataMapping::nullableDatetime($item, 'source_crawled_at')
        );
    }
}
