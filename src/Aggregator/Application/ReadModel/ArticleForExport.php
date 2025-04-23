<?php

declare(strict_types=1);

namespace App\Aggregator\Application\ReadModel;

use App\Aggregator\Domain\Model\Entity\Identity\ArticleId;
use DateTimeInterface;

/**
 * Class ExportedArticle.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class ArticleForExport implements \JsonSerializable
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

    #[\Override]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->toRfc4122(),
            'title' => $this->title,
            'link' => $this->link,
            'categories' => $this->categories,
            'body' => $this->body,
            'source' => $this->source,
            'hash' => $this->hash,
            'published_at' => $this->publishedAt->format(DateTimeInterface::RFC3339),
            'crawled_at' => $this->crawledAt->format(DateTimeInterface::RFC3339),
        ];
    }
}
