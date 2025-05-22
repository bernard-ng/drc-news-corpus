<?php

declare(strict_types=1);

namespace App\FeedManagement\Domain\Model\Entity;

use App\Aggregator\Domain\Model\Entity\Source;
use App\FeedManagement\Domain\Model\Identity\FollowedSourceId;
use App\IdentityAndAccess\Domain\Model\Entity\User;

/**
 * Class FollowedSource.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
readonly class FollowedSource
{
    public FollowedSourceId $id;

    public function __construct(
        public Source $source,
        public User $follower,
        public \DateTimeImmutable $createdAt = new \DateTimeImmutable(),
    ) {
        $this->id = new FollowedSourceId();
    }
}
