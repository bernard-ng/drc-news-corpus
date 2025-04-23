<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL;

use App\Aggregator\Application\ReadModel\SourceMetrics;
use App\Aggregator\Application\ReadModel\Statistics;
use App\Aggregator\Application\UseCase\Query\GetStatistics;
use App\Aggregator\Application\UseCase\QueryHandler\GetStatsHandler;
use App\Aggregator\Infrastructure\Persistence\Doctrine\CacheKey;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\Mapping;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\NoResult;
use Doctrine\DBAL\Cache\QueryCacheProfile;
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
    public function __invoke(GetStatistics $query): Statistics
    {
        $qb = $this->connexion->createQueryBuilder()
            ->select('COUNT(link) AS total, MAX(crawled_at) AS last_crawl_at, source')
            ->from('article')
            ->groupBy('source')
            ->orderBy('source', 'DESC')
            ->enableResultCache(new QueryCacheProfile(3600, CacheKey::STATISTICS->value))
        ;

        try {
            /** @var array{total: int, source: string, last_crawl_at: string}[] $data */
            $data = $qb->executeQuery()->fetchAllAssociative();

            return $this->mapStatistics($data);
        } catch (\Throwable $e) {
            throw NoResult::forQuery($qb->getSQL(), $qb->getParameters(), $e);
        }
    }

    private function mapStatistics(array $data): Statistics
    {
        return new Statistics(array_map(fn ($row) => new SourceMetrics(
            total: Mapping::integer($row, 'total'),
            source: Mapping::string($row, 'source'),
            lastCrawledAt: Mapping::string($row, 'last_crawl_at')
        ), $data));
    }
}
