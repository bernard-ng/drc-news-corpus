<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\ReadModel;

use App\SharedKernel\Domain\Assert;
use App\SharedKernel\Domain\Model\Pagination\PaginationInfo;

/**
 * Class CommentList.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class CommentList
{
    public function __construct(
        public array $items,
        public PaginationInfo $pagination
    ) {
        Assert::allIsInstanceOf($this->items, Comment::class);
    }

    public static function create(array $items, PaginationInfo $pagination): self
    {
        return new self(
            array_map(fn (array $item): Comment => Comment::create($item), $items),
            $pagination
        );
    }
}
