<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\ORM;

use App\Aggregator\Domain\Exception\ArticleNotFound;
use App\Aggregator\Domain\Model\Entity\Article;
use App\Aggregator\Domain\Model\Identity\ArticleId;
use App\Aggregator\Domain\Model\Repository\ArticleRepository;
use App\SharedKernel\Domain\Model\ValueObject\DateRange;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
    public function getById(ArticleId $id): Article
    {
        /** @var Article|null $article */
        $article = $this->findOneBy([
            'id' => $id,
        ]);

        if ($article === null) {
            throw ArticleNotFound::withId($id);
        }

        return $article;
    }

    #[\Override]
    public function export(?string $source, ?DateRange $date): \Generator
    {
        $qb = $this->createQueryBuilder('a')
            ->orderBy('a.publishedAt', 'DESC');

        if ($source !== null) {
            $qb
                ->leftJoin('a.source', 's')
                ->andWhere('s.name = :source')
                ->setParameter('source', $source);
        }

        if ($date instanceof DateRange) {
            $qb->andWhere('a.publishedAt BETWEEN FROM_UNIXTIME(:start) AND FROM_UNIXTIME(:end)')
                ->setParameter('start', $date->start)
                ->setParameter('end', $date->end);
        }

        $limit = 1000;
        $offset = 0;

        while (true) {
            $qb->setFirstResult($offset);
            $qb->setMaxResults($limit);

            /** @var Article[] $articles */
            $articles = $qb->getQuery()->getResult();
            if (count($articles) === 0) {
                break;
            }

            foreach ($articles as $article) {
                yield $article;
                $this->getEntityManager()->detach($article);
            }

            $offset += $limit;
        }
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

    #[\Override]
    public function clear(string $source, ?string $category): int
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.source', 's')
            ->where('s.name = :source')
            ->setParameter('source', $source);

        if ($category !== null) {
            $qb->andWhere('a.categories LIKE :category')
                ->setParameter('category', sprintf('%%%s%%', $category));
        }

        /** @var int $result */
        $result = $qb->delete(Article::class, 'a')
            ->getQuery()
            ->execute();

        return $result;
    }
}
