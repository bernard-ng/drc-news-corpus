<?php

declare(strict_types=1);

namespace App\Aggregator\Application\ReadModel;

use App\Aggregator\Application\ReadModel\Source\SourceReference;
use App\Aggregator\Domain\Model\Identity\ArticleId;
use App\Aggregator\Domain\Model\ValueObject\Link;
use App\Aggregator\Domain\Model\ValueObject\ReadingTime;

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
}
