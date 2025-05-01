<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Model\Entity;

use App\Aggregator\Domain\Model\Entity\Identity\ArticleId;
use App\Aggregator\Domain\Model\ValueObject\Credibility\Credibility;

/**
 * Class ArticleDetails.
 * This a scrapped article from a website.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
class Article
{
    public readonly ArticleId $id;

    public function __construct(
        public readonly string $title,
        public readonly string $link,
        public readonly string $body,
        public readonly string $hash,
        private(set) string $categories,
        public readonly Source $source,
        public readonly \DateTimeImmutable $publishedAt,
        public readonly \DateTimeImmutable $crawledAt,
        private(set) Credibility $credibility = new Credibility(),
        private(set) ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->id = new ArticleId();
    }

    public function defineCredibility(Credibility $credibility): self
    {
        $this->credibility = $credibility;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function assignCategories(string $categories): self
    {
        $this->categories = $categories;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }
}
