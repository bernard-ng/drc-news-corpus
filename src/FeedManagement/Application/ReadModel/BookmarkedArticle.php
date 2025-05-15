<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\ReadModel;

use App\Aggregator\Domain\Model\Identity\ArticleId;
use App\Aggregator\Domain\Model\ValueObject\Link;

/**
 * Class BookmarkedArticle.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class BookmarkedArticle implements \JsonSerializable
{
    public function __construct(
        public ArticleId $id,
        public string $title,
        public Link $link,
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
            'excerpt' => $this->excerpt,
            'source' => $this->source,
            'image' => $this->image,
            'publishedAt' => $this->publishedAt,
        ];
    }
}
