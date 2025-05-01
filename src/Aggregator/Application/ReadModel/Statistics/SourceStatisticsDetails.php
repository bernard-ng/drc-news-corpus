<?php

declare(strict_types=1);

namespace App\Aggregator\Application\ReadModel\Statistics;

/**
 * Class SourceStatisticsDetails.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class SourceStatisticsDetails implements \JsonSerializable
{
    public function __construct(
        public PublicationGraph $publications,
        public CategoryShares $categories,
        public SourceOverview $overview
    ) {
    }

    #[\Override]
    public function jsonSerialize(): array
    {
        return [
            'source' => $this->overview->source,
            'publicationsGraph' => $this->publications,
            'categoriesShares' => $this->categories,
            'categories' => $this->categories->total,
            'articles' => $this->overview->articles,
            'crawledAt' => $this->overview->crawledAt,
            'updatedAt' => $this->overview->updatedAt,
        ];
    }
}
