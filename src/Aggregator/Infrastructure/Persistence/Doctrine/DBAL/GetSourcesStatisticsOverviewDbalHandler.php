<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL;

use App\Aggregator\Application\ReadModel\Statistics\SourceOverview;
use App\Aggregator\Application\ReadModel\Statistics\SourcesStatisticsOverview;
use App\Aggregator\Application\UseCase\Query\GetSourcesStatisticsOverview;
use App\Aggregator\Application\UseCase\QueryHandler\GetSourcesStatisticsOverviewHandler;
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
final readonly class GetSourcesStatisticsOverviewDbalHandler implements GetSourcesStatisticsOverviewHandler
{
    public function __construct(
        private Connection $connexion
    ) {
    }

    #[\Override]
    public function __invoke(GetSourcesStatisticsOverview $query): SourcesStatisticsOverview
    {
        $qb = $this->connexion->createQueryBuilder()
            ->select('COUNT(link) AS total, MAX(crawled_at) AS crawled_at, source')
            ->addSelect('s.updated_at AS updated_at, s.url as url')
            ->addSelect('COUNT(CASE WHEN metadata IS NOT NULL THEN 1 ELSE NULL END) AS metadata_available')
            ->leftJoin('article', 'source', 's', 'article.source = s.name')
            ->from('article')
            ->groupBy('source')
            ->orderBy('total', 'DESC')
            ->enableResultCache(new QueryCacheProfile(3600, CacheKey::SOURCES_STATISTICS_OVERVIEW->value));

        try {
            /** @var array<array<string, mixed>> $data */
            $data = $qb->executeQuery()->fetchAllAssociative();

            return new SourcesStatisticsOverview(array_map(fn ($item): SourceOverview => new SourceOverview(
                articles: Mapping::integer($item, 'total'),
                source: Mapping::string($item, 'source'),
                url: Mapping::string($item, 'url'),
                crawledAt: Mapping::string($item, 'crawled_at'),
                updatedAt: Mapping::nullableDatetime($item, 'updated_at')?->format('Y-m-d H:i:s'),
                metadataAvailable: Mapping::integer($item, 'metadata_available')
            ), $data));
        } catch (\Throwable $e) {
            throw NoResult::forQuery($qb->getSQL(), $qb->getParameters(), $e);
        }
    }
}
