<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Entity;

/**
 * Class Article.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
readonly class Article
{
    public function __construct(
        public string $id,
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
