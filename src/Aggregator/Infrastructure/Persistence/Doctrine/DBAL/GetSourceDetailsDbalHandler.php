<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL;

use App\Aggregator\Application\ReadModel\Source\CategoryShare;
use App\Aggregator\Application\ReadModel\Source\CategoryShares;
use App\Aggregator\Application\ReadModel\Source\DailyEntry;
use App\Aggregator\Application\ReadModel\Source\PublicationGraph;
use App\Aggregator\Application\ReadModel\Source\SourceDetails;
use App\Aggregator\Application\UseCase\Query\GetSourceDetails;
use App\Aggregator\Application\UseCase\QueryHandler\GetSourceDetailsHandler;
use App\Aggregator\Domain\Exception\SourceNotFound;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Bias;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Credibility;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Reliability;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Transparency;
use App\Aggregator\Infrastructure\Persistence\Doctrine\CacheKey\SourceCacheKey;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\Mapping;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\NoResult;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Connection;

/**
 * Class GetSourceDetailsDbalHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetSourceDetailsDbalHandler implements GetSourceDetailsHandler
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function __invoke(GetSourceDetails $query): SourceDetails
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                's.name as source_name',
                's.description as source_description',
                's.url as source_url',
                's.updated_at as source_updated_at',
                's.display_name as source_display_name',
                's.bias as source_bias',
                's.reliability as source_reliability',
                's.transparency as source_transparency',
                'COUNT(a.link) AS articles_count',
                'MAX(a.crawled_at) AS source_crawled_at',
                'COUNT(CASE WHEN a.metadata IS NOT NULL THEN 1 ELSE NULL END) AS articles_metadata_available',
            )
            ->from('article', 'a')
            ->leftJoin('a', 'source', 's', 'a.source = s.name')
            ->where('a.source = :source')
            ->setParameter('source', $query->source)
            ->enableResultCache(new QueryCacheProfile(0, SourceCacheKey::SOURCE_DETAILS->withId($query->source)));

        try {
            $data = $qb->executeQuery()->fetchAssociative();
        } catch (\Throwable $e) {
            throw NoResult::forQuery($qb->getSQL(), $qb->getParameters(), $e);
        }

        if (empty($data['source_name'])) {
            throw SourceNotFound::withName($query->source);
        }

        return new SourceDetails(
            name: Mapping::string($data, 'source_name'),
            url: Mapping::string($data, 'source_url'),
            credibility: new Credibility(
                Mapping::enum($data, 'source_bias', Bias::class),
                Mapping::enum($data, 'source_reliability', Reliability::class),
                Mapping::enum($data, 'source_transparency', Transparency::class)
            ),
            publicationGraph: $this->getPublicationGraph($query),
            categoryShares: $this->getCategoryShares($query),
            articlesCount: Mapping::integer($data, 'articles_count'),
            crawledAt: Mapping::string($data, 'source_crawled_at'),
            displayName: Mapping::nullableString($data, 'source_display_name'),
            description: Mapping::nullableString($data, 'source_description'),
            updatedAt: Mapping::nullableString($data, 'source_updated_at'),
            metadataAvailable: Mapping::integer($data, 'articles_metadata_available'),
            followed: false // TODO: check if the user follows the source
        );
    }

    public function getPublicationGraph(GetSourceDetails $query): PublicationGraph
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('DATE(published_at) AS day, COUNT(*) AS count')
            ->from('article')
            ->where('published_at >= :start')
            ->andWhere('source = :source')
            ->setParameter('start', new \DateTimeImmutable('-12 months')->format('Y-m-d'))
            ->setParameter('source', $query->source)
            ->groupBy('day')
            ->orderBy('day', 'ASC')
            ->enableResultCache(new QueryCacheProfile(0, SourceCacheKey::SOURCE_PUBLICATION_GRAPH->withId($query->source)));

        try {
            /** @var array<array{day: string, count: int}> $data */
            $data = $qb->executeQuery()->fetchAllAssociative();
        } catch (\Throwable $e) {
            throw NoResult::forQuery($qb->getSQL(), $qb->getParameters(), $e);
        }

        if (empty($data)) {
            throw SourceNotFound::withName($query->source);
        }

        $countsByDate = [];
        foreach ($data as $row) {
            $countsByDate[$row['day']] = (int) $row['count'];
        }

        $start = new \DateTimeImmutable('-6 months');
        $end = new \DateTimeImmutable('today');
        $period = new \DatePeriod($start, new \DateInterval('P1D'), $end);

        $heatmap = [];
        foreach ($period as $date) {
            $day = $date->format('Y-m-d');
            $heatmap[] = new DailyEntry($day, $countsByDate[$day] ?? 0);
        }

        return new PublicationGraph($heatmap);
    }

    public function getCategoryShares(GetSourceDetails $query): CategoryShares
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('categories')
            ->from('article')
            ->where('source = :source')
            ->setParameter('source', $query->source)
            ->enableResultCache(new QueryCacheProfile(0, SourceCacheKey::SOURCE_CATEGORY_SHARES->withId($query->source)));

        try {
            /** @var array<string> $categories */
            $categories = $qb->executeQuery()->fetchFirstColumn();
        } catch (\Throwable $e) {
            throw NoResult::forQuery($qb->getSQL(), $qb->getParameters(), $e);
        }

        if (empty($categories)) {
            throw SourceNotFound::withName($query->source);
        }

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

        return new CategoryShares($shares, count($counts));
    }
}
