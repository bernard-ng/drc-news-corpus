<?php

declare(strict_types=1);

namespace App\Aggregator\Application\ReadModel\Statistics;

use App\SharedKernel\Domain\Assert;

/**
 * Class PublicationGraph.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class PublicationGraph implements \JsonSerializable
{
    public function __construct(
        public array $items = []
    ) {
        Assert::allIsInstanceOf($this->items, DailyEntry::class);
    }

    #[\Override]
    public function jsonSerialize(): array
    {
        return array_map(fn (DailyEntry $entry): array => [
            'date' => $entry->date,
            'count' => $entry->count,
        ], $this->items);
    }
}
