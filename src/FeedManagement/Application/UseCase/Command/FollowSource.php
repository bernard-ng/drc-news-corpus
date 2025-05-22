<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\UseCase\Command;

use App\IdentityAndAccess\Domain\Model\Identity\UserId;

/**
 * Class FollowSource.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class FollowSource
{
    public function __construct(
        public string $source,
        public UserId $userId
    ) {
    }
}
