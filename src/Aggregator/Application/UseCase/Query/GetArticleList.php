<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\Query;

use App\Aggregator\Domain\Model\ValueObject\Filters\ArticleFilters;
use App\SharedKernel\Domain\Model\ValueObject\Page;

/**
 * Class GetArticleList.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetArticleList
{
    public function __construct(
        public ArticleFilters $filters,
        public Page $page
    ) {
    }
}
