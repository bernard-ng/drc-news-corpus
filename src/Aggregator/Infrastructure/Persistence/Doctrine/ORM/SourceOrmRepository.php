<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\ORM;

use App\Aggregator\Domain\Exception\SourceNotFound;
use App\Aggregator\Domain\Model\Entity\Source;
use App\Aggregator\Domain\Model\Identity\SourceId;
use App\Aggregator\Domain\Model\Repository\SourceRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class SourceOrmRepository.
 *
 * @extends ServiceEntityRepository<Source>
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class SourceOrmRepository extends ServiceEntityRepository implements SourceRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Source::class);
    }

    public function add(Source $source): void
    {
        $this->getEntityManager()->persist($source);
        $this->getEntityManager()->flush();
    }

    public function remove(Source $source): void
    {
        $this->getEntityManager()->remove($source);
        $this->getEntityManager()->flush();
    }

    public function getByName(string $name): Source
    {
        $source = $this->findOneBy([
            'name' => $name,
        ]);

        if ($source === null) {
            throw SourceNotFound::withName($name);
        }

        return $source;
    }

    public function getById(SourceId $sourceId): Source
    {
        $source = $this->findOneBy([
            'id' => $sourceId,
        ]);

        if ($source === null) {
            throw SourceNotFound::withId($sourceId);
        }

        return $source;
    }
}
