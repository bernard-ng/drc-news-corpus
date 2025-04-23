<?php

declare(strict_types=1);

namespace App\Aggregator\Application\ReadModel;

use App\SharedKernel\Domain\Assert;
use App\SharedKernel\Domain\Model\ValueObject\Pagination;

/**
 * Class ArticleList.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class ArticleList
{
    public function __construct(
        public array $items,
        public Pagination $pagination
    ) {
        Assert::allIsInstanceOf($this->items, ArticleDetails::class);
    }
}
