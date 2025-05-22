<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL;

use App\Aggregator\Application\ReadModel\ArticleOverview;
use App\Aggregator\Application\ReadModel\ArticleOverviewList;
use App\Aggregator\Application\ReadModel\Source\SourceReference;
use App\Aggregator\Application\UseCase\Query\GetArticleOverviewList;
use App\Aggregator\Application\UseCase\QueryHandler\GetArticleOverviewListHandler;
use App\Aggregator\Domain\Model\Identity\ArticleId;
use App\Aggregator\Domain\Model\ValueObject\Crawling\DateRange;
use App\Aggregator\Domain\Model\ValueObject\Crawling\OpenGraph;
use App\Aggregator\Domain\Model\ValueObject\Link;
use App\Aggregator\Domain\Model\ValueObject\ReadingTime;
use App\SharedKernel\Application\Asset\AssetType;
use App\SharedKernel\Domain\Model\Filters\FiltersQuery;
use App\SharedKernel\Domain\Model\ValueObject\Pagination;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\Mapping;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\NoResult;
use App\SharedKernel\Infrastructure\Persistence\Filesystem\Asset\AssetUrlProvider;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class GetArticleOverviewListDbalHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetArticleOverviewListDbalHandler implements GetArticleOverviewListHandler
{
    public function __construct(
        private Connection $connection,
        private PaginatorInterface $paginator,
        private AssetUrlProvider $assetUrlProvider,
    ) {
    }

    #[\Override]
    public function __invoke(GetArticleOverviewList $query): ArticleOverviewList
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'a.id as article_id',
                'a.title as article_title',
                'a.link as article_link',
                'a.categories as article_categories',
                'LEFT(a.body, 200) as article_excerpt', // Not sure if this is optimal, benchmark needed
                'a.published_at as article_published_at',
                'a.metadata as article_metadata',
                'a.reading_time as article_reading_time',
            )
            ->addSelect(
                's.display_name as source_display_name',
                's.url as source_url',
                's.name as source_name',
            )
            ->addSelect('CASE WHEN b.id IS NOT NULL THEN TRUE ELSE FALSE END as article_is_bookmarked')
            ->from('article', 'a')
            ->leftJoin('a', 'source', 's', 'a.source = s.name')
            ->leftJoin('a', 'bookmark_article', 'ba', 'a.id = ba.article_id')
            ->leftJoin('ba', 'bookmark', 'b', 'ba.bookmark_id = b.id AND b.user_id = :userId')
            ->setParameter('userId', $query->userId->toBinary(), ParameterType::BINARY)
            ->orderBy('article_published_at', 'DESC');

        $qb = $this->applyFilters($qb, $query->filters);

        try {
            /** @var SlidingPaginationInterface<int, array<string, mixed>> $data */
            $data = $this->paginator->paginate($qb, $query->page->page, $query->page->limit);
        } catch (\Throwable $e) {
            throw NoResult::forQuery($qb->getSQL(), $qb->getParameters(), $e);
        }

        return $this->mapArticleOverviewList($data);
    }

    private function applyFilters(QueryBuilder $qb, FiltersQuery $filters): QueryBuilder
    {
        if ($filters->source !== null) {
            $qb->andWhere('source_name = :source')
                ->setParameter('source', $filters->source);
        }

        if ($filters->category !== null) {
            $qb->andWhere('article_categories LIKE :category')
                ->setParameter('category', sprintf('%%%s%%', $filters->category));
        }

        if ($filters->search !== null) {
            $qb->andWhere('article_title LIKE :search')
                ->setParameter('search', sprintf('%%%s%%', $filters->search));
        }

        if ($filters->dateRange instanceof DateRange) {
            $qb->andWhere('article_published_at BETWEEN FROM_UNIXTIME(:start) AND FROM_UNIXTIME(:end)')
                ->setParameter('start', $filters->dateRange->start, ParameterType::INTEGER)
                ->setParameter('end', $filters->dateRange->end, ParameterType::INTEGER);
        }

        return $qb;
    }

    /**
     * @param SlidingPaginationInterface<int, array<string, mixed>> $data
     */
    private function mapArticleOverviewList(SlidingPaginationInterface $data): ArticleOverviewList
    {
        return new ArticleOverviewList(
            items: array_map(
                fn ($item): ArticleOverview => $this->mapArticleOverview($item),
                \iterator_to_array($data->getItems())
            ),
            pagination: Pagination::create($data->getPaginationData())
        );
    }

    private function mapArticleOverview(array $item): ArticleOverview
    {
        $openGraph = OpenGraph::tryFrom(Mapping::nullableString($item, 'article_metadata'));

        return new ArticleOverview(
            ArticleId::fromBinary($item['article_id']),
            Mapping::string($item, 'article_title'),
            Link::from(Mapping::string($item, 'article_link')),
            explode(',', Mapping::string($item, 'article_categories')),
            trim(Mapping::string($item, 'article_excerpt')),
            new SourceReference(
                Mapping::string($item, 'source_name'),
                Mapping::nullableString($item, 'source_display_name'),
                $this->assetUrlProvider->getUrl(
                    Mapping::string($item, 'source_name'),
                    AssetType::SOURCE_PROFILE_IMAGE
                ),
                Mapping::string($item, 'source_url'),
            ),
            $openGraph?->image,
            ReadingTime::create(Mapping::nullableInteger($item, 'article_reading_time')),
            Mapping::datetime($item, 'article_published_at'),
            Mapping::boolean($item, 'article_is_bookmarked'),
        );
    }
}
