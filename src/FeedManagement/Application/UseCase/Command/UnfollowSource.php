<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\UseCase\Command;

use App\IdentityAndAccess\Domain\Model\Identity\UserId;

/**
 * Class UnfollowSource.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class UnfollowSource
{
    public function __construct(
        public string $source,
        public UserId $userId
    ) {
    }
}
