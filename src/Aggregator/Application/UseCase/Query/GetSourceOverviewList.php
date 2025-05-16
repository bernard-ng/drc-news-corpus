<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\Query;

use App\IdentityAndAccess\Domain\Model\Identity\UserId;
use App\SharedKernel\Domain\Model\Filters\FiltersQuery;
use App\SharedKernel\Domain\Model\ValueObject\Page;

/**
 * Class GetSourceOverviewList.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetSourceOverviewList
{
    public function __construct(
        public FiltersQuery $filters = new FiltersQuery(),
        public Page $page = new Page(),
        public ?UserId $userId = null,
    ) {
    }
}
