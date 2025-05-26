<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL;

use App\Aggregator\Application\ReadModel\ArticleOverviewList;
use App\Aggregator\Application\UseCase\Query\GetArticleOverviewList;
use App\Aggregator\Application\UseCase\QueryHandler\GetArticleOverviewListHandler;
use App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL\Features\ArticleQuery;
use App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL\Features\BookmarkQuery;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\NoResult;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class GetArticleOverviewListDbalHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetArticleOverviewListDbalHandler implements GetArticleOverviewListHandler
{
    use BookmarkQuery;
    use ArticleQuery;

    public function __construct(
        private Connection $connection,
        private PaginatorInterface $paginator,
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
                "JSON_VALUE(a.metadata, '$.image') as article_image",
                'a.reading_time as article_reading_time',
            )
            ->addSelect(
                's.display_name as source_display_name',
                "CONCAT('https://devscast.org/images/sources/', s.name, '.png') as source_image",
                's.url as source_url',
                's.name as source_name',
            )
            ->addSelect(sprintf('%s as article_is_bookmarked', $this->isArticleBookmarkedQuery()))
            ->from('article', 'a')
            ->innerJoin('a', 'source', 's', 'a.source = s.name')
            ->setParameter('userId', $query->userId->toBinary(), ParameterType::BINARY)
            ->orderBy('article_published_at', 'DESC');

        $qb = $this->applyArticleFilters($qb, $query->filters);

        try {
            /** @var SlidingPaginationInterface<int, array<string, mixed>> $data */
            $data = $this->paginator->paginate($qb, $query->page->page, $query->page->limit);
        } catch (\Throwable $e) {
            throw NoResult::forQuery($qb->getSQL(), $qb->getParameters(), $e);
        }

        return ArticleOverviewList::create($data->getItems(), $data->getPaginationData());
    }
}
