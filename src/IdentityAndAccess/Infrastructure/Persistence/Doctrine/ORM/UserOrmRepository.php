<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Infrastructure\Persistence\Doctrine\ORM;

use App\IdentityAndAccess\Domain\Exception\UserNotFound;
use App\IdentityAndAccess\Domain\Model\Entity\Identity\UserId;
use App\IdentityAndAccess\Domain\Model\Entity\User;
use App\IdentityAndAccess\Domain\Model\Repository\UserRepository;
use App\IdentityAndAccess\Domain\Model\ValueObject\Secret\TimedToken;
use App\SharedKernel\Domain\Model\ValueObject\Email;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class UserOrmRepository.
 *
 * @extends ServiceEntityRepository<User>
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class UserOrmRepository extends ServiceEntityRepository implements UserRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    #[\Override]
    public function add(User $user): void
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    #[\Override]
    public function remove(User $user): void
    {
        $this->getEntityManager()->remove($user);
        $this->getEntityManager()->flush();
    }

    #[\Override]
    public function getById(UserId $id): void
    {
        /** @var User|null $user */
        $user = $this->findOneBy([
            'id' => $id,
        ]);

        if ($user === null) {
            throw UserNotFound::withId($id);
        }
    }

    #[\Override]
    public function getByEmail(Email $email): ?User
    {
        return $this->findOneBy([
            'email' => $email,
        ]);
    }

    #[\Override]
    public function getByResetToken(TimedToken $token): User
    {
        /** @var User|null $user */
        $user = $this->findOneBy([
            'passwordResetToken.value' => $token->token,
        ]);

        if ($user === null) {
            throw new UserNotFound();
        }

        return $user;
    }
}
