<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\ReadModel;

use App\SharedKernel\Domain\Assert;
use App\SharedKernel\Domain\Model\Pagination\PaginationInfo;

/**
 * Class BookmarkList.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class BookmarkList
{
    public function __construct(
        public array $items,
        public PaginationInfo $pagination
    ) {
        Assert::allIsInstanceOf($this->items, Bookmark::class);
    }

    public static function create(array $items, PaginationInfo $pagination): self
    {
        return new self(
            array_map(fn (array $item): Bookmark => Bookmark::create($item), $items),
            $pagination
        );
    }
}
