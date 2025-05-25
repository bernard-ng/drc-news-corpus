<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\UseCase\Query;

use App\IdentityAndAccess\Domain\Model\Identity\UserId;

/**
 * Class GetUserProfile.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetUserProfile
{
    public function __construct(
        public UserId $userId
    ) {
    }
}
