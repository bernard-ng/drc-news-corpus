<?php

declare(strict_types=1);

namespace App\Aggregator\Application\ReadModel;

use App\Aggregator\Domain\Model\Identity\ArticleId;
use App\Aggregator\Domain\Model\ValueObject\Link;

/**
 * Class ArticleOverview.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class ArticleOverview implements \JsonSerializable
{
    public function __construct(
        public ArticleId $id,
        public string $title,
        public Link $link,
        public array $categories,
        public string $excerpt,
        public string $source,
        public ?string $image,
        public \DateTimeImmutable $publishedAt,
    ) {
    }

    #[\Override]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'link' => (string) $this->link,
            'categories' => $this->categories,
            'excerpt' => $this->excerpt,
            'source' => $this->source,
            'image' => $this->image,
            'publishedAt' => $this->publishedAt,
        ];
    }
}
