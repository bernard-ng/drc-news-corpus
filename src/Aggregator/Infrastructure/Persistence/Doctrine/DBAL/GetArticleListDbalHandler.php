<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL;

use App\Aggregator\Application\ReadModel\ArticleList;
use App\Aggregator\Application\UseCase\Query\GetArticleList;
use App\Aggregator\Application\UseCase\QueryHandler\GetArticleListHandler;
use App\Aggregator\Domain\Model\ValueObject\Filters\ArticleFilters;
use App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL\Features\ArticleQuery;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\NoResult;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class GetArticleListDbalHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetArticleListDbalHandler implements GetArticleListHandler
{
    use ArticleQuery;

    public function __construct(
        private Connection $connection,
        private PaginatorInterface $paginator
    ) {
    }

    #[\Override]
    public function __invoke(GetArticleList $query): ArticleList
    {
        $qb = $this->createArticleBaseQuery()
            ->orderBy('published_at', 'DESC');

        $qb = $this->applyFilters($qb, $query->filters);

        try {
            /** @var SlidingPaginationInterface<int, array<string, mixed>> $data */
            $data = $this->paginator->paginate($qb, $query->page->page, $query->page->limit);
        } catch (\Throwable $e) {
            throw NoResult::forQuery($qb->getSQL(), $qb->getParameters(), $e);
        }

        return $this->mapArticleList($data);
    }

    private function applyFilters(QueryBuilder $qb, ArticleFilters $filters): QueryBuilder
    {
        if ($filters->source !== null) {
            $qb->andWhere('source = :source')
                ->setParameter('source', $filters->source);
        }

        if ($filters->category !== null) {
            $qb->andWhere('categories LIKE :category')
                ->setParameter('category', "%{$filters->category}%");
        }

        if ($filters->search !== null) {
            $qb->andWhere('title LIKE :search OR body LIKE :search')
                ->setParameter('search', "%{$filters->search}%");
        }

        if ($filters->dateRange !== null) {
            $qb->andWhere('published_at BETWEEN FROM_UNIXTIME(:start) AND FROM_UNIXTIME(:end)')
                ->setParameter('start', $filters->dateRange->start, ParameterType::INTEGER)
                ->setParameter('end', $filters->dateRange->end, ParameterType::INTEGER);
        }

        return $qb;
    }
}
