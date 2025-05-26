<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL\Features;

use App\Aggregator\Domain\Model\ValueObject\Crawling\DateRange;
use App\SharedKernel\Domain\Model\Filters\FiltersQuery;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Trait ArticleQuery.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
trait ArticleQuery
{
    private function getArticleLastId(): string
    {
        return $this->connection->createQueryBuilder()
            ->select('a.id')
            ->from('article', 'a')
            ->orderBy('a.id', 'DESC')
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchOne();
    }

    /**
     * Applies filters to the provided QueryBuilder instance based on the given FiltersQuery.
     *
     * @param QueryBuilder $qb The query builder instance to which filters will be applied.
     * @param FiltersQuery $filters The filters containing criteria for filtering articles.
     *
     * @return QueryBuilder The updated query builder with the applied filters.
     */
    private function applyArticleFilters(QueryBuilder $qb, FiltersQuery $filters): QueryBuilder
    {
        if ($filters->source !== null) {
            $qb->andWhere('s.name = :source')
                ->setParameter('source', $filters->source);
        }

        if ($filters->category !== null) {
            $qb->andWhere('a.categories LIKE :category')
                ->setParameter('category', sprintf('%%%s%%', $filters->category));
        }

        if ($filters->search !== null) {
            $qb->andWhere('a.title LIKE :search')
                ->setParameter('search', sprintf('%%%s%%', $filters->search));
        }

        if ($filters->dateRange instanceof DateRange) {
            $qb->andWhere('a.published_at BETWEEN FROM_UNIXTIME(:start) AND FROM_UNIXTIME(:end)')
                ->setParameter('start', $filters->dateRange->start, ParameterType::INTEGER)
                ->setParameter('end', $filters->dateRange->end, ParameterType::INTEGER);
        }

        return $qb;
    }
}
