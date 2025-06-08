<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\ReadModel;

use App\SharedKernel\Domain\Assert;
use App\SharedKernel\Domain\Model\Pagination\PaginationInfo;

/**
 * Class ArticleOverviewList.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class ArticleOverviewList
{
    public function __construct(
        public array $items,
        public PaginationInfo $pagination
    ) {
        Assert::allIsInstanceOf($this->items, ArticleOverview::class);
    }

    public static function create(array $items, PaginationInfo $pagination): self
    {
        return new self(
            array_map(fn (array $item): ArticleOverview => ArticleOverview::create($item), $items),
            $pagination
        );
    }
}
