<?php

declare(strict_types=1);

namespace App\Aggregator\Application\ReadModel;

use Symfony\Component\Uid\Uuid;

/**
 * Class Article.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class Article
{
    public function __construct(
        public Uuid $id,
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
