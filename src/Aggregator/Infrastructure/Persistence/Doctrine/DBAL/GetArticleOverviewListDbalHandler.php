<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL;

use App\Aggregator\Application\ReadModel\ArticleOverviewList;
use App\Aggregator\Application\UseCase\Query\GetArticleOverviewList;
use App\Aggregator\Application\UseCase\QueryHandler\GetArticleOverviewListHandler;
use App\Aggregator\Domain\Model\ValueObject\Crawling\DateRange;
use App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL\Features\ArticleQuery;
use App\SharedKernel\Domain\Model\Filters\FiltersQuery;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\NoResult;
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
    use ArticleQuery;

    public function __construct(
        private Connection $connection,
        private PaginatorInterface $paginator
    ) {
    }

    #[\Override]
    public function __invoke(GetArticleOverviewList $query): ArticleOverviewList
    {
        $qb = $this->createArticleOverviewBaseQuery()
            ->orderBy('published_at', 'DESC');

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
            $qb->andWhere('source = :source')
                ->setParameter('source', $filters->source);
        }

        if ($filters->category !== null) {
            $qb->andWhere('categories LIKE :category')
                ->setParameter('category', sprintf('%%%s%%', $filters->category));
        }

        if ($filters->search !== null) {
            $qb->andWhere('title LIKE :search OR body LIKE :search')
                ->setParameter('search', sprintf('%%%s%%', $filters->search));
        }

        if ($filters->dateRange instanceof DateRange) {
            $qb->andWhere('published_at BETWEEN FROM_UNIXTIME(:start) AND FROM_UNIXTIME(:end)')
                ->setParameter('start', $filters->dateRange->start, ParameterType::INTEGER)
                ->setParameter('end', $filters->dateRange->end, ParameterType::INTEGER);
        }

        return $qb;
    }
}
