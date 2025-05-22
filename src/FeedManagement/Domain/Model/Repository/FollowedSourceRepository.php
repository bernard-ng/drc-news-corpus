<?php

declare(strict_types=1);

namespace App\FeedManagement\Domain\Model\Repository;

use App\FeedManagement\Domain\Model\Entity\FollowedSource;
use App\IdentityAndAccess\Domain\Model\Identity\UserId;

/**
 * Interface FollowedSourceRepository.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface FollowedSourceRepository
{
    public function add(FollowedSource $followedSource): void;

    public function remove(FollowedSource $followedSource): void;

    public function getByUserId(UserId $userId, string $source): ?FollowedSource;
}
