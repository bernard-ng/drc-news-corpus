<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Model\Entity;

use App\Aggregator\Domain\Model\Entity\Identity\ArticleId;

/**
 * Class ArticleDetails.
 * This a scrapped article from a website.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
readonly class Article
{
    public ArticleId $id;

    public function __construct(
        public string $title,
        public string $link,
        public string $categories,
        public string $body,
        public string $source,
        public string $hash,
        public \DateTimeImmutable $publishedAt,
        public \DateTimeImmutable $crawledAt
    ) {
        $this->id = new ArticleId();
    }
}
