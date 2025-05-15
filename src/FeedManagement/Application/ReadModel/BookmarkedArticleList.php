<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\ReadModel;

use App\SharedKernel\Domain\Assert;
use App\SharedKernel\Domain\Model\ValueObject\Pagination;

/**
 * Class BookmarkedArticleList.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class BookmarkedArticleList
{
    public function __construct(
        public array $items,
        public Pagination $pagination
    ) {
        Assert::allIsInstanceOf($this->items, BookmarkedArticle::class);
    }
}
