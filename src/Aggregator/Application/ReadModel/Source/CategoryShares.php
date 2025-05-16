<?php

declare(strict_types=1);

namespace App\Aggregator\Application\ReadModel\Source;

use App\SharedKernel\Domain\Assert;

/**
 * Class CategoryShares.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class CategoryShares implements \JsonSerializable
{
    public function __construct(
        public array $items = [],
        public int $total = 0
    ) {
        Assert::allIsInstanceOf($this->items, CategoryShare::class);
    }

    #[\Override]
    public function jsonSerialize(): array
    {
        return array_map(fn (CategoryShare $share): array => [
            'name' => $share->category,
            'count' => $share->count,
            'percentage' => $share->percentage,
        ], $this->items);
    }
}
