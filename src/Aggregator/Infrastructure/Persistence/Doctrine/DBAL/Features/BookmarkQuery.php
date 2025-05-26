<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL\Features;

/**
 * Class BookmarkQuery.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
trait BookmarkQuery
{
    /**
     * Returns a SQL query to check if a user bookmarks an article.
     */
    private function isArticleBookmarkedQuery(): string
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('1')
            ->from('bookmark_article', 'ba')
            ->innerJoin('ba', 'bookmark', 'b', 'ba.bookmark_id = b.id')
            ->where('ba.article_id = a.id')
            ->andWhere('b.user_id = :userId');

        return sprintf('EXISTS (%s)', $qb->getSQL());
    }
}
