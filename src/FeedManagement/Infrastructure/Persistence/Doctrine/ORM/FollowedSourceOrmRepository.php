<?php

declare(strict_types=1);

namespace App\FeedManagement\Infrastructure\Persistence\Doctrine\ORM;

use App\Aggregator\Domain\Model\Identity\SourceId;
use App\FeedManagement\Domain\Model\Entity\FollowedSource;
use App\FeedManagement\Domain\Model\Repository\FollowedSourceRepository;
use App\IdentityAndAccess\Domain\Model\Identity\UserId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class FollowedSourceOrmRepository.
 *
 * @extends ServiceEntityRepository<FollowedSource>
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class FollowedSourceOrmRepository extends ServiceEntityRepository implements FollowedSourceRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FollowedSource::class);
    }

    public function add(FollowedSource $followedSource): void
    {
        $this->getEntityManager()->persist($followedSource);
        $this->getEntityManager()->flush();
    }

    public function remove(FollowedSource $followedSource): void
    {
        $this->getEntityManager()->remove($followedSource);
        $this->getEntityManager()->flush();
    }

    public function getByUserId(UserId $userId, SourceId $sourceId): ?FollowedSource
    {
        return $this->createQueryBuilder('fs')
            ->andWhere('fs.follower = :userId')
            ->andWhere('fs.source = :sourceId')
            ->setParameter('sourceId', $sourceId->toBinary(), ParameterType::BINARY)
            ->setParameter('userId', $userId->toBinary(), ParameterType::BINARY)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
