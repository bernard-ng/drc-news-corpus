<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL;

use App\Aggregator\Application\ReadModel\SourceStatisticsList;
use App\Aggregator\Application\UseCase\Query\GetSourceStatisticsList;
use App\Aggregator\Application\UseCase\QueryHandler\GetSourceStatisticsListHandler;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\NoResult;
use Doctrine\DBAL\Connection;

/**
 * Class GetSourceStatisticsListDbalHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetSourceStatisticsListDbalHandler implements GetSourceStatisticsListHandler
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function __invoke(GetSourceStatisticsList $query): SourceStatisticsList
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                's.id as source_id',
                's.name as source_name',
                'MAX(a.crawled_at) as source_crawled_at',
                'COUNT(a.id) as articles_count',
                'SUM(CASE WHEN a.metadata IS NOT NULL THEN 1 ELSE 0 END) as article_metadata_available'
            )
            ->from('source', 's')
            ->leftJoin('s', 'article', 'a', 'a.source_id = s.id')
            ->groupBy('s.id')
            ->orderBy('s.name', 'ASC');

        try {
            $data = $qb->executeQuery()->fetchAllAssociative();
        } catch (\Throwable $e) {
            throw NoResult::forQuery($qb->getSQL(), $qb->getParameters(), $e);
        }

        return SourceStatisticsList::create($data);
    }
}
