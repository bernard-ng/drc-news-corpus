<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Domain\Model\Repository;

use App\IdentityAndAccess\Domain\Model\Entity\LoginHistory;
use App\IdentityAndAccess\Domain\Model\Entity\User;

/**
 * Interface LoginHistoryRepository.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface LoginHistoryRepository
{
    public function add(LoginHistory $loginHistory): void;

    public function remove(LoginHistory $loginHistory): void;

    public function getLastBy(User $user): ?LoginHistory;
}
