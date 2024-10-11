<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL;

use App\Aggregator\Application\ReadModel\SourceStatistics;
use App\Aggregator\Application\UseCase\Query\GetStatsHandler;
use App\Aggregator\Application\UseCase\Query\GetStatsQuery;
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
        try {
            $qb = $this->connexion->createQueryBuilder()
                ->select('COUNT(link) AS total, MAX(crawled_at) AS last_crawl_at, source')
                ->from('article')
                ->groupBy('source')
                ->orderBy('source', 'DESC');

            /** @var array{total: int, source: string, last_crawl_at: string}[] $result */
            $result = $qb->executeQuery()->fetchAllAssociative();

            return array_map(fn ($row) => new SourceStatistics(
                total: (int) $row['total'],
                source: $row['source'],
                lastCrawledAt: $row['last_crawl_at']
            ), $result);
        } catch (\Throwable $e) {
            throw new \RuntimeException('An error occurred while fetching stats', previous: $e);
        }
    }
}
