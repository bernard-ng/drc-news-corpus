<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\ReadModel;

use App\Aggregator\Domain\Model\Identity\ArticleId;
use App\Aggregator\Domain\Model\ValueObject\Link;
use App\Aggregator\Domain\Model\ValueObject\ReadingTime;
use App\SharedKernel\Domain\DataTransfert\DataMapping;

/**
 * Class ArticleOverview.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class ArticleOverview
{
    public function __construct(
        public ArticleId $id,
        public string $title,
        public Link $link,
        public array $categories,
        public string $excerpt,
        public SourceReference $source,
        public ?string $image,
        public ReadingTime $readingTime,
        public \DateTimeImmutable $publishedAt,
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
            trim(DataMapping::string($item, 'article_excerpt')),
            SourceReference::create($item),
            DataMapping::nullableString($item, 'article_image'),
            ReadingTime::create(DataMapping::nullableInteger($item, 'article_reading_time')),
            DataMapping::datetime($item, 'article_published_at'),
            DataMapping::boolean($item, 'article_is_bookmarked'),
        );
    }
}
