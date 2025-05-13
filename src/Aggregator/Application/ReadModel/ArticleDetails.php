<?php

declare(strict_types=1);

namespace App\Aggregator\Application\ReadModel;

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
final readonly class ArticleDetails implements \JsonSerializable
{
    public function __construct(
        public ArticleId $id,
        public string $title,
        public Link $link,
        public array $categories,
        public string $body,
        public string $source,
        public string $hash,
        public Credibility $credibility,
        public Sentiment $sentiment,
        public ?OpenGraph $metadata,
        public ReadingTime $readingTime,
        public \DateTimeImmutable $publishedAt,
        public \DateTimeImmutable $crawledAt,
        public ?\DateTimeImmutable $updatedAt
    ) {
    }

    #[\Override]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'link' => (string) $this->link,
            'categories' => $this->categories,
            'body' => $this->body,
            'source' => $this->source,
            'hash' => $this->hash,
            'credibility' => $this->credibility,
            'sentiment' => $this->sentiment,
            'metadata' => $this->metadata,
            'readingTime' => (string) $this->readingTime,
            'publishedAt' => $this->publishedAt,
            'crawledAt' => $this->crawledAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}
