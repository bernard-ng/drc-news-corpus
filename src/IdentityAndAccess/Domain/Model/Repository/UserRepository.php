<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Domain\Model\Repository;

use App\IdentityAndAccess\Domain\Model\Entity\Identity\UserId;
use App\IdentityAndAccess\Domain\Model\Entity\User;
use App\IdentityAndAccess\Domain\Model\ValueObject\Secret\TimedToken;
use App\SharedKernel\Domain\Model\ValueObject\Email;
use Symfony\Component\Uid\Uuid;

/**
 * Interface UserRepository.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface UserRepository
{
    public function add(User $user): void;

    public function remove(User $user): void;

    public function getById(UserId $id): void;

    public function getByEmail(Email $email): ?User;

    public function getByResetToken(TimedToken $token): User;
}
