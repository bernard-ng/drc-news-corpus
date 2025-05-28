<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\UseCase\Command;

use App\Aggregator\Domain\Model\Identity\SourceId;
use App\IdentityAndAccess\Domain\Model\Identity\UserId;

/**
 * Class UnfollowSource.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class UnfollowSource
{
    public function __construct(
        public SourceId $sourceId,
        public UserId $userId
    ) {
    }
}
