<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL\Features;

/**
 * Trait SourceQuery.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
trait SourceQuery
{
    /**
     * Returns a SQL query to check if a user follows a source.
     */
    private function isSourceFollowedQuery(): string
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('1')
            ->from('followed_source', 'f')
            ->where('f.source = s.name')
            ->andWhere('f.follower_id = :userId');

        return sprintf('EXISTS (%s)', $qb->getSQL());
    }
}
