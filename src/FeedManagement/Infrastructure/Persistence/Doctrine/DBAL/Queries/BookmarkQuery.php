<?php

declare(strict_types=1);

namespace App\FeedManagement\Infrastructure\Persistence\Doctrine\DBAL\Queries;

use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Class BookmarkQuery.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
trait BookmarkQuery
{
    private function addBookmarkSelectQuery(QueryBuilder $qb): QueryBuilder
    {
        return $qb->addSelect(
            'b.id AS bookmark_id',
            'b.name AS bookmark_name',
            'b.description AS bookmark_description',
            'b.created_at AS bookmark_created_at',
            'b.updated_at AS bookmark_updated_at',
            'COUNT(ba.article_id) AS bookmark_articles_count',
            'b.is_public AS bookmark_is_public'
        );
    }

    private function addArticleBookmarkedExistsQuery(QueryBuilder $qb): QueryBuilder
    {
        $subQb = $this->connection->createQueryBuilder()
            ->select('1')
            ->from('bookmark_article', 'ba')
            ->innerJoin('ba', 'bookmark', 'b', 'ba.bookmark_id = b.id')
            ->where('ba.article_id = a.id')
            ->andWhere('b.user_id = :userId');
        $query = sprintf('EXISTS (%s)', $subQb->getSQL());

        return $qb->addSelect(sprintf('%s as article_is_bookmarked', $query));
    }
}
