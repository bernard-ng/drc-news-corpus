<?php

declare(strict_types=1);

namespace App\Aggregator\Application\ReadModel;

use App\Aggregator\Domain\Model\Entity\Identity\ArticleId;

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
        public string $categories,
        public string $body,
        public string $source,
        public string $hash,
        public \DateTimeImmutable $publishedAt,
        public \DateTimeImmutable $crawledAt
    ) {
    }
}
