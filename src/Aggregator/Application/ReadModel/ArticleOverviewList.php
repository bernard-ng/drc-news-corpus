<?php

declare(strict_types=1);

namespace App\Aggregator\Application\ReadModel;

use App\SharedKernel\Domain\Assert;
use App\SharedKernel\Domain\Model\ValueObject\Pagination;

/**
 * Class ArticleOverviewList.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class ArticleOverviewList
{
    public function __construct(
        public array $items,
        public Pagination $pagination
    ) {
        Assert::allIsInstanceOf($this->items, ArticleOverview::class);
    }
}
