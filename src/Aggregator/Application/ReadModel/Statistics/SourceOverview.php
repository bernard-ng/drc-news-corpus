<?php

declare(strict_types=1);

namespace App\Aggregator\Application\ReadModel\Statistics;

/**
 * Class SourceOverview.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class SourceOverview implements \JsonSerializable
{
    public function __construct(
        public int $articles,
        public string $source,
        public string $crawledAt,
        public ?string $updatedAt = null
    ) {
    }

    #[\Override]
    public function jsonSerialize(): array
    {
        return [
            'articles' => $this->articles,
            'source' => $this->source,
            'crawledAt' => $this->crawledAt,
            'updatedAt' => $this->updatedAt
        ];
    }
}
