<?php

declare(strict_types=1);

namespace App\Aggregator\Application\ReadModel\Source;

use App\Aggregator\Domain\Model\ValueObject\Scoring\Credibility;

/**
 * Class SourceDetails.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class SourceDetails
{
    public function __construct(
        public string $name,
        public string $url,
        public Credibility $credibility,
        public PublicationGraph $publicationGraph,
        public CategoryShares $categoryShares,
        public int $articlesCount,
        public string $crawledAt,
        public ?string $displayName = null,
        public ?string $description = null,
        public ?string $updatedAt = null,
        public int $metadataAvailable = 0,
        public bool $followed = false,
        public ?string $image = null,
    ) {
    }
}
