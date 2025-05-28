<?php

declare(strict_types=1);

namespace App\Aggregator\Application\ReadModel;

use App\SharedKernel\Domain\Assert;

/**
 * Class SourceStatisticsList.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class SourceStatisticsList
{
    public function __construct(
        public array $items,
    ) {
        Assert::allIsInstanceOf($items, SourceStatistics::class);
    }

    public static function create(array $items): self
    {
        return new self(
            array_map(fn (array $item): SourceStatistics => SourceStatistics::create($item), $items),
        );
    }
}
