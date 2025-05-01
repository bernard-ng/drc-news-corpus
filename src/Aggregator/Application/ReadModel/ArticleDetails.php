<?php

declare(strict_types=1);

namespace App\Aggregator\Application\ReadModel;

use App\Aggregator\Domain\Model\Entity\Identity\ArticleId;
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
        public string $link,
        public array $categories,
        public string $body,
        public string $source,
        public string $hash,
        public Credibility $credibility,
        public Sentiment $sentiment,
        public \DateTimeImmutable $publishedAt,
        public \DateTimeImmutable $crawledAt,
        public ?\DateTimeImmutable $updatedAt
    ) {
    }
}
