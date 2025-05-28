<?php

declare(strict_types=1);

namespace App\FeedManagement\Infrastructure\Persistence\Doctrine\DBAL;

use App\Aggregator\Domain\Exception\SourceNotFound;
use App\FeedManagement\Application\Cache\SourceCacheAttributes;
use App\FeedManagement\Application\ReadModel\CategoryShare;
use App\FeedManagement\Application\ReadModel\CategoryShares;
use App\FeedManagement\Application\ReadModel\PublicationEntry;
use App\FeedManagement\Application\ReadModel\PublicationGraph;
use App\FeedManagement\Application\ReadModel\SourceDetails;
use App\FeedManagement\Application\UseCase\Query\GetSourceDetails;
use App\FeedManagement\Application\UseCase\QueryHandler\GetSourceDetailsHandler;
use App\FeedManagement\Infrastructure\Persistence\Doctrine\DBAL\Queries\SourceQuery;
use App\SharedKernel\Domain\Model\ValueObject\DateRange;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\NoResult;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Class GetSourceDetailsDbalHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetSourceDetailsDbalHandler implements GetSourceDetailsHandler
{
    use SourceQuery;

    private const int PUBLICATION_GRAPH_DAYS = 180;

    public function __construct(
        private Connection $connection,
        private CacheInterface $cache
    ) {
    }

    #[\Override]
    public function __invoke(GetSourceDetails $query): SourceDetails
    {
        $qb = $this->connection->createQueryBuilder();
        $qb = $this->addSourceDetailsSelectQuery($qb);
        $qb = $this->addFollowedSourceExistsQuery($qb);

        $qb->from('source', 's')
            ->leftJoin('s', 'article', 'a', 'a.source_id = s.id')
            ->where('s.id = :sourceId')
            ->setParameter('sourceId', $query->sourceId->toBinary(), ParameterType::BINARY)
            ->setParameter('userId', $query->userId->toBinary(), ParameterType::BINARY);

        try {
            $data = $qb->executeQuery()->fetchAssociative();
        } catch (\Throwable $e) {
            throw NoResult::forQuery($qb->getSQL(), $qb->getParameters(), $e);
        }

        if (empty($data['source_id'])) {
            throw SourceNotFound::withId($query->sourceId);
        }

        return SourceDetails::create(
            $data,
            $this->getPublicationGraph($query),
            $this->getCategoryShares($query)
        );
    }

    public function getPublicationGraph(GetSourceDetails $query): PublicationGraph
    {
        $cacheKey = SourceCacheAttributes::PUBLICATIONS->withId($query->sourceId->toString());
        $dateRange = DateRange::backward(days: self::PUBLICATION_GRAPH_DAYS);

        $qb = $this->connection->createQueryBuilder()
            ->select('DATE(a.published_at) AS day, COUNT(a.id) AS count')
            ->from('article', 'a')
            ->innerJoin('a', 'source', 's', 'a.source_id = s.id')
            ->where(' s.id = :sourceId')
            ->andWhere('a.published_at BETWEEN FROM_UNIXTIME(:start) AND FROM_UNIXTIME(:end)')
            ->groupBy('day')
            ->orderBy('day', 'ASC')
            ->setParameter('sourceId', $query->sourceId->toBinary(), ParameterType::BINARY)
            ->setParameter('start', $dateRange->start, ParameterType::INTEGER)
            ->setParameter('end', $dateRange->end, ParameterType::INTEGER)
            ->enableResultCache(new QueryCacheProfile(SourceCacheAttributes::CACHE_TTL, $cacheKey));

        try {
            /** @var list<array{day: string, count: int}> $data */
            $data = $qb->executeQuery()->fetchAllAssociative();
        } catch (\Throwable $e) {
            throw NoResult::forQuery($qb->getSQL(), $qb->getParameters(), $e);
        }

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($data, $dateRange): PublicationGraph {
            $item->expiresAfter(SourceCacheAttributes::CACHE_TTL);

            $countsByDate = [];
            foreach ($data as $row) {
                $countsByDate[$row['day']] = (int) $row['count'];
            }

            $start = \DateTimeImmutable::createFromTimestamp($dateRange->start);
            $end = \DateTimeImmutable::createFromTimestamp($dateRange->end);
            $period = new \DatePeriod($start, new \DateInterval('P1D'), $end);

            $heatmap = [];
            foreach ($period as $date) {
                $day = $date->format('Y-m-d');
                $heatmap[] = new PublicationEntry($day, $countsByDate[$day] ?? 0);
            }

            return new PublicationGraph($heatmap, count($heatmap));
        });
    }

    public function getCategoryShares(GetSourceDetails $query): CategoryShares
    {
        $cacheKey = SourceCacheAttributes::CATEGORIES->withId($query->sourceId->toString());
        $qb = $this->connection->createQueryBuilder()
            ->select('a.categories')
            ->from('article', 'a')
            ->innerJoin('a', 'source', 's', 'a.source_id = s.id')
            ->where('s.id = :sourceId')
            ->setParameter('sourceId', $query->sourceId->toBinary(), ParameterType::BINARY)
            ->enableResultCache(new QueryCacheProfile(SourceCacheAttributes::CACHE_TTL, $cacheKey));

        try {
            /** @var array<string> $categories */
            $categories = $qb->executeQuery()->fetchFirstColumn();
        } catch (\Throwable $e) {
            throw NoResult::forQuery($qb->getSQL(), $qb->getParameters(), $e);
        }

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($categories): CategoryShares {
            $item->expiresAfter(SourceCacheAttributes::CACHE_TTL);

            $counts = [];
            foreach ($categories as $row) {
                foreach (array_filter(array_map('trim', explode(',', $row))) as $cat) {
                    $counts[$cat] = ($counts[$cat] ?? 0) + 1;
                }
            }

            $total = array_sum($counts);

            $shares = array_map(
                fn (string $category, int $count): CategoryShare => new CategoryShare(
                    category: $category,
                    count: $count,
                    percentage: $total > 0 ? round(($count / $total) * 100, 2) : 0.0
                ),
                array_keys($counts),
                array_values($counts)
            );
            usort($shares, fn (CategoryShare $a, CategoryShare $b): int => ($a->count <=> $b->count) * -1);

            return new CategoryShares($shares, count($counts));
        });
    }
}
