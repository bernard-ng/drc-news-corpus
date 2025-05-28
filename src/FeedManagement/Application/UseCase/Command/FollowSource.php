<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\UseCase\Command;

use App\Aggregator\Domain\Model\Identity\SourceId;
use App\IdentityAndAccess\Domain\Model\Identity\UserId;

/**
 * Class FollowSource.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class FollowSource
{
    public function __construct(
        public SourceId $sourceId,
        public UserId $userId
    ) {
    }
}
