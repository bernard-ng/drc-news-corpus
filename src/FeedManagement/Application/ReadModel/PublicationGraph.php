<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\ReadModel;

use App\SharedKernel\Domain\Assert;

/**
 * Class PublicationGraph.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class PublicationGraph
{
    public function __construct(
        public array $items = [],
        public int $total = 0
    ) {
        Assert::allIsInstanceOf($this->items, PublicationEntry::class);
    }
}
