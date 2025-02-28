<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL;

use App\Aggregator\Application\ReadModel\SourceStatistics;
use App\Aggregator\Application\UseCase\Query\GetStatsQuery;
use App\Aggregator\Application\UseCase\QueryHandler\GetStatsHandler;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\Mapping;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\NoResult;
use Doctrine\DBAL\Connection;

/**
 * Class GetStatsDbalHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetStatsDbalHandler implements GetStatsHandler
{
    public function __construct(
        private Connection $connexion
    ) {
    }

    #[\Override]
    public function __invoke(GetStatsQuery $query): array
    {
        $qb = $this->connexion->createQueryBuilder()
            ->select('COUNT(link) AS total, MAX(crawled_at) AS last_crawl_at, source')
            ->from('article')
            ->groupBy('source')
            ->orderBy('source', 'DESC');

        try {
            /** @var array{total: int, source: string, last_crawl_at: string}[] $data */
            $data = $qb->executeQuery()->fetchAllAssociative();

            return array_map(fn ($row) => new SourceStatistics(
                total: Mapping::integer($row, 'total'),
                source: Mapping::string($row, 'source'),
                lastCrawledAt: Mapping::string($row, 'last_crawl_at')
            ), $data);
        } catch (\Throwable $e) {
            throw NoResult::forQuery($qb->getSQL(), $qb->getParameters(), $e);
        }
    }
}
