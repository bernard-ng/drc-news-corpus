<?php

declare(strict_types=1);

namespace App\FeedManagement\Infrastructure\Persistence\Doctrine\DBAL\Queries;

use App\FeedManagement\Domain\Model\Filters\ArticleFilters;
use App\SharedKernel\Domain\Model\ValueObject\DateRange;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Trait ArticleQuery.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
trait ArticleQuery
{
    private function addArticleOverviewSelectQuery(QueryBuilder $qb): QueryBuilder
    {
        return $qb->addSelect(
            'a.id as article_id',
            'a.title as article_title',
            'a.link as article_link',
            'a.categories as article_categories',
            'a.excerpt as article_excerpt',
            'a.published_at as article_published_at',
            'a.image as article_image',
            'a.reading_time as article_reading_time',
        );
    }

    private function addArticleDetailsSelectQuery(QueryBuilder $qb): QueryBuilder
    {
        return $qb->addSelect(
            'a.id as article_id',
            'a.title as article_title',
            'a.link as article_link',
            'a.categories as article_categories',
            'a.body as article_body',
            'a.hash as article_hash',
            'a.published_at as article_published_at',
            'a.crawled_at as article_crawled_at',
            'a.updated_at as article_updated_at',
            'a.bias as article_bias',
            'a.reliability as article_reliability',
            'a.transparency as article_transparency',
            'a.sentiment as article_sentiment',
            'a.metadata as article_metadata',
            'a.reading_time as article_reading_time',
        );
    }

    /**
     * Applies filters to the provided QueryBuilder instance based on the given ArticleFilters.
     *
     * @param QueryBuilder $qb The query builder instance to which filters will be applied.
     * @param ArticleFilters $filters The filters containing criteria for filtering articles.
     *
     * @return QueryBuilder The updated query builder with the applied filters.
     */
    private function applyArticleFilters(QueryBuilder $qb, ArticleFilters $filters): QueryBuilder
    {
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
