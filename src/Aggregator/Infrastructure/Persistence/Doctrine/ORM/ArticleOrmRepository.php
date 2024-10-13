<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\ORM;

use App\Aggregator\Domain\Entity\Article;
use App\Aggregator\Domain\Repository\ArticleRepository;
use App\Aggregator\Domain\ValueObject\DateRange;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * Class ArticleOrmRepository.
 *
 * @extends ServiceEntityRepository<Article>
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class ArticleOrmRepository extends ServiceEntityRepository implements ArticleRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    #[\Override]
    public function add(Article $article): void
    {
        $this->getEntityManager()->persist($article);
        $this->getEntityManager()->flush();
    }

    #[\Override]
    public function remove(Article $article): void
    {
        $this->getEntityManager()->remove($article);
        $this->getEntityManager()->flush();
    }

    #[\Override]
    public function countBySource(string $source): int
    {
        return $this->count([
            'source' => $source,
        ]);
    }

    #[\Override]
    public function getById(string $id): ?Article
    {
        /** @var Article|null $article */
        $article = $this->findOneBy([
            'id' => Uuid::fromString($id),
        ]);

        return $article;
    }

    #[\Override]
    public function getByLink(string $link): ?Article
    {
        /** @var Article|null $article */
        $article = $this->findOneBy([
            'link' => $link,
        ]);

        return $article;
    }

    #[\Override]
    public function export(?string $source, ?DateRange $date): array
    {
        $qb = $this->createQueryBuilder('a')
            ->orderBy('a.publishedAt', 'DESC');

        if ($source !== null) {
            $qb->andWhere('a.source = :source')
                ->setParameter('source', $source);
        }

        if ($date !== null) {
            $qb->andWhere('a.publishedAt BETWEEN :start AND :end')
                ->setParameter('start', $date->start)
                ->setParameter('end', $date->end);
        }

        /** @var Article[] $result */
        $result = $qb
            ->getQuery()
            ->getResult();

        return $result;
    }

    #[\Override]
    public function getByHash(string $hash): ?Article
    {
        /** @var Article|null $article */
        $article = $this->findOneBy([
            'hash' => $hash,
        ]);

        return $article;
    }
}
