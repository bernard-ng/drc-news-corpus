<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL;

use App\Aggregator\Application\ReadModel\ArticleForExport;
use App\Aggregator\Application\UseCase\Query\GetArticlesForExport;
use App\Aggregator\Application\UseCase\QueryHandler\GetArticlesForExportHandler;
use App\SharedKernel\Domain\Model\ValueObject\DateRange;
use Doctrine\DBAL\Connection;

/**
 * Class GetArticlesForExportDbalHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetArticlesForExportDbalHandler implements GetArticlesForExportHandler
{
    private const int BATCH_SIZE = 1000;

    public function __construct(
        private Connection $connection
    ) {
    }

    #[\Override]
    public function __invoke(GetArticlesForExport $query): iterable
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'a.id as article_id',
                'a.title as article_title',
                'a.link as article_link',
                'a.categories as article_categories',
                'a.body as article_body',
                's.name as article_source',
                'a.hash as article_hash',
                'a.published_at as article_published_at',
                'a.crawled_at as article_crawled_at'
            )
            ->from('article', 'a')
            ->innerJoin('a', 'source', 's', 'a.source_id = s.id')
            ->orderBy('a.published_at', 'DESC');

        if ($query->source !== null) {
            $qb->andWhere('s.name = :source')
                ->setParameter('source', $query->source);
        }

        if ($query->date instanceof DateRange) {
            $qb->andWhere('a.published_at BETWEEN :start AND :end')
                ->setParameter('start', $query->date->start)
                ->setParameter('end', $query->date->end);
        }

        $offset = 0;

        while (true) {
            $qb->setFirstResult($offset);
            $qb->setMaxResults(self::BATCH_SIZE);

            /** @var array<array<string, mixed>> $data */
            $data = $qb->executeQuery()->fetchAllAssociative();
            if (count($data) === 0) {
                break;
            }

            foreach ($data as $article) {
                yield ArticleForExport::create($article);
            }

            $offset += self::BATCH_SIZE;
        }
    }
}
