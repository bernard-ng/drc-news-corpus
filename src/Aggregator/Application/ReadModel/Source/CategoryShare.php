<?php

declare(strict_types=1);

namespace App\Aggregator\Application\ReadModel\Source;

/**
 * Class CategoryShare.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class CategoryShare implements \JsonSerializable
{
    public function __construct(
        public string $category,
        public int $count,
        public float $percentage
    ) {
    }

    #[\Override]
    public function jsonSerialize(): array
    {
        return [
            'category' => $this->category,
            'count' => $this->count,
            'percentage' => round($this->percentage, 2),
        ];
    }
}
