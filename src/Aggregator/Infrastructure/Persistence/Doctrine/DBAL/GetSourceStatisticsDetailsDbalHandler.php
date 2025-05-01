<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL;

use App\Aggregator\Application\ReadModel\Statistics\CategoryShare;
use App\Aggregator\Application\ReadModel\Statistics\CategoryShares;
use App\Aggregator\Application\ReadModel\Statistics\DailyEntry;
use App\Aggregator\Application\ReadModel\Statistics\PublicationGraph;
use App\Aggregator\Application\ReadModel\Statistics\SourceOverview;
use App\Aggregator\Application\ReadModel\Statistics\SourceStatisticsDetails;
use App\Aggregator\Application\UseCase\Query\GetSourceStatisticsDetails;
use App\Aggregator\Application\UseCase\QueryHandler\GetSourceStatisticsDetailsHandler;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\Mapping;
use Doctrine\DBAL\Connection;

/**
 * Class GetSourceStatisticsDbalHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetSourceStatisticsDetailsDbalHandler implements GetSourceStatisticsDetailsHandler
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function __invoke(GetSourceStatisticsDetails $query): SourceStatisticsDetails
    {
        return new SourceStatisticsDetails(
            $this->getPublicationGraph($query),
            $this->getCategoryShares($query),
            $this->getSourceOverview($query)
        );
    }

    public function getPublicationGraph(GetSourceStatisticsDetails $query): PublicationGraph
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('DATE(published_at) AS day, COUNT(*) AS count')
            ->from('article')
            ->where('published_at >= :start')
            ->andWhere('source = :source')
            ->setParameter('start', new \DateTimeImmutable('-12 months')->format('Y-m-d'))
            ->setParameter('source', $query->source)
            ->groupBy('day')
            ->orderBy('day', 'ASC');

        /** @var array<array{day: string, count: int}> $data */
        $data = $qb->executeQuery()->fetchAllAssociative();

        $start = new \DateTimeImmutable('-12 months');
        $end = new \DateTimeImmutable('today');
        $period = new \DatePeriod($start, new \DateInterval('P1D'), $end);

        $countsByDate = [];
        foreach ($data as $row) {
            $countsByDate[$row['day']] = (int) $row['count'];
        }

        $heatmap = [];
        foreach ($period as $date) {
            $day = $date->format('Y-m-d');
            $heatmap[] = [
                'day' => $day,
                'count' => $countsByDate[$day] ?? 0,
            ];
        }

        return new PublicationGraph(array_map(
            fn ($entry) => new DailyEntry($entry['day'], $entry['count']),
            $heatmap
        ));
    }

    public function getCategoryShares(GetSourceStatisticsDetails $query): CategoryShares
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('categories')
            ->from('article')
            ->where('source = :source')
            ->setParameter('source', $query->source);

        /** @var array<string> $categories */
        $categories = $qb->executeQuery()->fetchFirstColumn();

        $counts = [];
        foreach ($categories as $category) {
            $categories = array_map('trim', explode(',', $category));
            foreach ($categories as $cat) {
                if ($cat === '') {
                    continue;
                }
                $counts[$cat] = ($counts[$cat] ?? 0) + 1;
            }
        }

        $total = array_sum($counts);

        return new CategoryShares(array_map(
            fn (string $category, int $count) => new CategoryShare(
                category: $category,
                count: $count,
                percentage: $total > 0 ? ($count / $total) * 100 : 0.0
            ),
            array_keys($counts),
            array_values($counts)
        ), count($counts));
    }

    public function getSourceOverview(GetSourceStatisticsDetails $query): SourceOverview
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('COUNT(link) AS total, MAX(crawled_at) AS crawled_at, source')
            ->addSelect('s.updated_at AS updated_at')
            ->leftJoin('article', 'source', 's', 'article.source = s.name')
            ->from('article')
            ->where('source = :source')
            ->setParameter('source', $query->source);

        /** @var array{total: int, source: string, crawled_at: string, updated_at: string|null} $data */
        $data = $qb->executeQuery()->fetchAssociative();

        return new SourceOverview(
            articles: Mapping::integer($data, 'total'),
            source: Mapping::string($data, 'source'),
            crawledAt: Mapping::string($data, 'crawled_at'),
            updatedAt: Mapping::nullableDatetime($data, 'updated_at')?->format('Y-m-d H:i:s')
        );
    }
}
