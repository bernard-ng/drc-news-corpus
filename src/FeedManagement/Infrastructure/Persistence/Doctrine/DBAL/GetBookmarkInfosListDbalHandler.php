<?php

declare(strict_types=1);

namespace App\FeedManagement\Infrastructure\Persistence\Doctrine\DBAL;

use App\FeedManagement\Application\ReadModel\BookmarkInfosList;
use App\FeedManagement\Application\UseCase\Query\GetBookmarkInfosList;
use App\FeedManagement\Application\UseCase\QueryHandler\GetBookmarkInfosListHandler;
use App\FeedManagement\Infrastructure\Persistence\Doctrine\CacheKey\BookmarkCacheKey;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\Features\PaginationQuery;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\NoResult;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Class GetBookmarkInfosListDbalHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetBookmarkInfosListDbalHandler implements GetBookmarkInfosListHandler
{
    use PaginationQuery;

    public function __construct(
        private Connection $connection,
    ) {
    }

    public function __invoke(GetBookmarkInfosList $query): BookmarkInfosList
    {
        $qb = $this->connection->createQueryBuilder()
            ->from('bookmark', 'b')
            ->select(
                'b.id AS bookmark_id',
                'b.name AS bookmark_name',
                'b.description AS bookmark_description',
                'b.created_at AS bookmark_created_at',
                'b.updated_at AS bookmark_updated_at',
                'COUNT(ba.article_id) AS bookmark_articles_count',
                'b.is_public AS bookmark_is_public'
            )
            ->leftJoin('b', 'bookmark_article', 'ba', 'ba.bookmark_id = b.id')
            ->where('b.user_id = :userId')
            ->groupBy('b.id')
            ->orderBy('b.created_at', 'DESC')
            ->setParameter('userId', $query->userId->toBinary(), ParameterType::BINARY)
            ->enableResultCache(new QueryCacheProfile(0, BookmarkCacheKey::BOOKMARK_LIST->withId($query->userId->toString())));

        $qb = $this->paginate($qb, $query);

        try {
            /** @var array<int, array<string, mixed>> $data */
            $data = $qb->executeQuery()->fetchAllAssociative();
        } catch (\Throwable $e) {
            throw NoResult::forQuery($qb->getSQL(), $qb->getParameters(), $e);
        }

        $pagination = $this->getPagination($data, $query->page, 'bookmark_id');
        return BookmarkInfosList::create($data, $pagination);
    }

    private function paginate(QueryBuilder $qb, GetBookmarkInfosList $query): QueryBuilder
    {
        return $this->applyCursorPagination($qb, $query->page, 'b.id', fn () => $this->connection->createQueryBuilder()
            ->select('b.id')
            ->from('bookmark', 'b')
            ->where('b.user_id = :userId')
            ->orderBy('b.id', 'DESC')
            ->setMaxResults(1)
            ->setParameter('userId', $query->userId->toBinary(), ParameterType::BINARY)
            ->executeQuery()
            ->fetchOne());
    }
}
