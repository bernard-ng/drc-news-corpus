<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\UseCase\Query;

use App\IdentityAndAccess\Domain\Model\Identity\UserId;
use App\SharedKernel\Domain\Model\Pagination\Page;

/**
 * Class GetBookmarkList.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetBookmarkList
{
    public function __construct(
        public UserId $userId,
        public Page $page = new Page(),
    ) {
    }
}
