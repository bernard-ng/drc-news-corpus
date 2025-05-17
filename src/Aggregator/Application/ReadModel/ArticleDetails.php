<?php

declare(strict_types=1);

namespace App\Aggregator\Application\ReadModel;

use App\Aggregator\Application\ReadModel\Source\SourceReference;
use App\Aggregator\Domain\Model\Identity\ArticleId;
use App\Aggregator\Domain\Model\ValueObject\Crawling\OpenGraph;
use App\Aggregator\Domain\Model\ValueObject\Link;
use App\Aggregator\Domain\Model\ValueObject\ReadingTime;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Credibility;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Sentiment;

/**
 * Class ArticleDetails.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class ArticleDetails
{
    public function __construct(
        public ArticleId $id,
        public string $title,
        public Link $link,
        public array $categories,
        public string $body,
        public SourceReference $source,
        public string $hash,
        public Credibility $credibility,
        public Sentiment $sentiment,
        public ?OpenGraph $metadata,
        public ReadingTime $readingTime,
        public \DateTimeImmutable $publishedAt,
        public \DateTimeImmutable $crawledAt,
        public ?\DateTimeImmutable $updatedAt,
        public bool $bookmarked = false
    ) {
    }
}
