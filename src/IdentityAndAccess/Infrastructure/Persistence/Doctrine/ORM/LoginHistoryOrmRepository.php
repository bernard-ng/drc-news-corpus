<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Infrastructure\Persistence\Doctrine\ORM;

use App\IdentityAndAccess\Domain\Model\Entity\LoginHistory;
use App\IdentityAndAccess\Domain\Model\Entity\User;
use App\IdentityAndAccess\Domain\Model\Repository\LoginHistoryRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class LoginHistoryOrmRepository.
 *
 * @extends ServiceEntityRepository<LoginHistory>
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class LoginHistoryOrmRepository extends ServiceEntityRepository implements LoginHistoryRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LoginHistory::class);
    }

    #[\Override]
    public function add(LoginHistory $loginHistory): void
    {
        $this->getEntityManager()->persist($loginHistory);
        $this->getEntityManager()->flush();
    }

    #[\Override]
    public function remove(LoginHistory $loginHistory): void
    {
        $this->getEntityManager()->remove($loginHistory);
        $this->getEntityManager()->flush();
    }

    #[\Override]
    public function getLastBy(User $user): ?LoginHistory
    {
        /** @var LoginHistory|null $loginHistory */
        $loginHistory = $this->createQueryBuilder('lh')
            ->andWhere('lh.user = :user')
            ->setParameter('user', $user)
            ->orderBy('lh.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $loginHistory;
    }
}
