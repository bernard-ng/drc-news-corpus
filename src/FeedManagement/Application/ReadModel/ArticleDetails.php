<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\ReadModel;

use App\Aggregator\Domain\Model\Identity\ArticleId;
use App\Aggregator\Domain\Model\ValueObject\Crawling\OpenGraph;
use App\Aggregator\Domain\Model\ValueObject\Link;
use App\Aggregator\Domain\Model\ValueObject\ReadingTime;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Bias;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Credibility;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Reliability;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Sentiment;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Transparency;
use App\SharedKernel\Domain\DataTransfert\DataMapping;

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

    public static function create(array $item): self
    {
        return new self(
            ArticleId::fromBinary($item['article_id']),
            DataMapping::string($item, 'article_title'),
            Link::from(DataMapping::string($item, 'article_link')),
            explode(',', DataMapping::string($item, 'article_categories')),
            DataMapping::string($item, 'article_body'),
            SourceReference::create($item),
            DataMapping::string($item, 'article_hash'),
            new Credibility(
                DataMapping::enum($item, 'article_bias', Bias::class),
                DataMapping::enum($item, 'article_reliability', Reliability::class),
                DataMapping::enum($item, 'article_transparency', Transparency::class)
            ),
            DataMapping::enum($item, 'article_sentiment', Sentiment::class),
            OpenGraph::tryFrom(DataMapping::nullableString($item, 'article_metadata')),
            ReadingTime::create(DataMapping::nullableInteger($item, 'article_reading_time')),
            DataMapping::datetime($item, 'article_published_at'),
            DataMapping::datetime($item, 'article_crawled_at'),
            DataMapping::nullableDatetime($item, 'article_updated_at'),
            DataMapping::boolean($item, 'article_is_bookmarked'),
        );
    }
}
