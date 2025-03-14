<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Model\Entity;

use Symfony\Component\Uid\Uuid;

/**
 * Class Article.
 * This a scrapped article from a website.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
readonly class Article
{
    public Uuid $id;

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
        $this->id = Uuid::v7();
    }
}
