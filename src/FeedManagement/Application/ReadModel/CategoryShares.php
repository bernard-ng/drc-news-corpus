<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\ReadModel;

use App\SharedKernel\Domain\Assert;

/**
 * Class CategoryShares.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class CategoryShares
{
    public function __construct(
        public array $items = [],
        public int $total = 0
    ) {
        Assert::allIsInstanceOf($this->items, CategoryShare::class);
    }
}
