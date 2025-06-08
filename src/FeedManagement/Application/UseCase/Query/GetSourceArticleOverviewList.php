<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\UseCase\Query;

use App\Aggregator\Domain\Model\Identity\SourceId;
use App\FeedManagement\Domain\Model\Filters\ArticleFilters;
use App\IdentityAndAccess\Domain\Model\Identity\UserId;
use App\SharedKernel\Domain\Model\Pagination\Page;

/**
 * Class GetArticleOverviewList.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetSourceArticleOverviewList
{
    public function __construct(
        public SourceId $sourceId,
        public UserId $userId,
        public Page $page = new Page(),
        public ArticleFilters $filters = new ArticleFilters(),
    ) {
    }
}
