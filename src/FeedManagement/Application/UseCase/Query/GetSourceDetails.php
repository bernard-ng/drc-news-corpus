<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\UseCase\Query;

use App\Aggregator\Domain\Model\Identity\SourceId;
use App\IdentityAndAccess\Domain\Model\Identity\UserId;

/**
 * Class GetSourceDetails.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetSourceDetails
{
    public function __construct(
        public SourceId $sourceId,
        public UserId $userId,
    ) {
    }
}
