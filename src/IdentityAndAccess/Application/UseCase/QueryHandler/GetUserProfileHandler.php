<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\UseCase\QueryHandler;

use App\IdentityAndAccess\Application\ReadModel\UserProfile;
use App\IdentityAndAccess\Application\UseCase\Query\GetUserProfile;
use App\SharedKernel\Application\Messaging\QueryHandler;

/**
 * Interface GetUserProfileHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface GetUserProfileHandler extends QueryHandler
{
    public function __invoke(GetUserProfile $query): UserProfile;
}
