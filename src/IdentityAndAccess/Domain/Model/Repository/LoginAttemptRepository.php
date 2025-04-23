<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Domain\Model\Repository;

use App\IdentityAndAccess\Domain\Model\Entity\LoginAttempt;
use App\IdentityAndAccess\Domain\Model\Entity\User;

/**
 * Interface LoginAttemptRepository.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface LoginAttemptRepository
{
    public function add(LoginAttempt $loginAttempt): void;

    public function remove(LoginAttempt $loginAttempt): void;

    public function countBy(User $user): int;

    public function deleteBy(User $user): void;
}
