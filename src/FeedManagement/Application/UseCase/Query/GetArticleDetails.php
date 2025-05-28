<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\UseCase\Query;

use App\Aggregator\Domain\Model\Identity\ArticleId;
use App\IdentityAndAccess\Domain\Model\Identity\UserId;

/**
 * Class GetArticleDetails.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetArticleDetails
{
    public function __construct(
        public ArticleId $id,
        public UserId $userId
    ) {
    }
}
