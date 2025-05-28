<?php

declare(strict_types=1);

namespace App\FeedManagement\Infrastructure\Persistence\Doctrine\DBAL\Queries;

use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Trait SourceQuery.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
trait SourceQuery
{
    private function addSourceOverviewSelectQuery(QueryBuilder $qb): QueryBuilder
    {
        return $qb
            ->addSelect(
                's.id as source_id',
                's.display_name as source_display_name',
                "CONCAT('https://devscast.org/images/sources/', s.name, '.png') as source_image",
                's.url as source_url',
                's.name as source_name',
            );
    }

    private function addSourceDetailsSelectQuery(QueryBuilder $qb): QueryBuilder
    {
        return $qb->addSelect(
            's.id as source_id',
            's.name as source_name',
            's.description as source_description',
            's.url as source_url',
            's.updated_at as source_updated_at',
            's.display_name as source_display_name',
            's.bias as source_bias',
            's.reliability as source_reliability',
            's.transparency as source_transparency',
            "CONCAT('https://devscast.org/images/sources/', s.name, '.png') as source_image",
            'COUNT(a.hash) AS articles_count',
            'MAX(a.crawled_at) AS source_crawled_at',
            'COUNT(CASE WHEN a.metadata IS NOT NULL THEN 1 ELSE NULL END) AS articles_metadata_available',
        );
    }

    private function addFollowedSourceExistsQuery(QueryBuilder $qb): QueryBuilder
    {
        $subQb = $this->connection->createQueryBuilder()
            ->select('1')
            ->from('followed_source', 'f')
            ->where('f.source_id = s.id')
            ->andWhere('f.follower_id = :userId');
        $query = sprintf('EXISTS (%s)', $subQb->getSQL());

        return $qb->addSelect(sprintf('%s as source_is_followed', $query));
    }
}
