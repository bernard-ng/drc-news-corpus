<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL;

use App\Aggregator\Application\ReadModel\ExportedArticle;
use App\Aggregator\Application\UseCase\Query\ExportQuery;
use App\Aggregator\Application\UseCase\QueryHandler\ExportHandler;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\Mapping;
use Doctrine\DBAL\Connection;

/**
 * Class ExportDbalHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class ExportDbalHandler implements ExportHandler
{
    public function __construct(
        private Connection $connection
    ) {
    }

    #[\Override]
    public function __invoke(ExportQuery $query): iterable
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('a.id', 'a.title', 'a.link', 'a.categories', 'a.body', 'a.source', 'a.hash', 'a.published_at', 'a.crawled_at')
            ->from('article', 'a')
            ->orderBy('a.published_at', 'DESC');

        if ($query->source !== null) {
            $qb->andWhere('a.source = :source')
                ->setParameter('source', $query->source);
        }

        if ($query->date !== null) {
            $qb->andWhere('a.published_at BETWEEN :start AND :end')
                ->setParameter('start', $query->date->start)
                ->setParameter('end', $query->date->end);
        }

        $limit = 1000;
        $offset = 0;

        while (true) {
            $qb->setFirstResult($offset);
            $qb->setMaxResults($limit);

            /** @var array<array<string, mixed>> $data */
            $data = $qb->executeQuery()->fetchAllAssociative();
            if (count($data) === 0) {
                break;
            }

            foreach ($data as $article) {
                yield new ExportedArticle(
                    Mapping::uuid($article, 'id'),
                    Mapping::string($article, 'title'),
                    Mapping::string($article, 'link'),
                    Mapping::string($article, 'categories'),
                    Mapping::string($article, 'body'),
                    Mapping::string($article, 'source'),
                    Mapping::string($article, 'hash'),
                    Mapping::datetime($article, 'published_at'),
                    Mapping::datetime($article, 'crawled_at')
                );
            }

            $offset += $limit;
        }
    }
}
