<?php

declare(strict_types=1);

namespace App\FeedManagement\Infrastructure\Persistence\Doctrine\DBAL;

use App\FeedManagement\Application\ReadModel\BookmarkInfosList;
use App\FeedManagement\Application\UseCase\Query\GetBookmarkInfosList;
use App\FeedManagement\Application\UseCase\QueryHandler\GetBookmarkInfosListHandler;
use App\FeedManagement\Infrastructure\Persistence\Doctrine\CacheKey\BookmarkCacheKey;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\NoResult;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class GetBookmarkInfosListDbalHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetBookmarkInfosListDbalHandler implements GetBookmarkInfosListHandler
{
    public function __construct(
        private Connection $connection,
        private PaginatorInterface $paginator
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

        try {
            /** @var SlidingPaginationInterface<int, array<string, mixed>> $data */
            $data = $this->paginator->paginate($qb, $query->page->page, $query->page->limit);
        } catch (\Throwable $e) {
            throw NoResult::forQuery($qb->getSQL(), $qb->getParameters(), $e);
        }

        return BookmarkInfosList::create($data->getItems(), $data->getPaginationData());
    }
}
