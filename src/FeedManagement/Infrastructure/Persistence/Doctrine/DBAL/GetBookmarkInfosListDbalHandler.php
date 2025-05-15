<?php

declare(strict_types=1);

namespace App\FeedManagement\Infrastructure\Persistence\Doctrine\DBAL;

use App\FeedManagement\Application\ReadModel\BookmarkInfos;
use App\FeedManagement\Application\ReadModel\BookmarkInfosList;
use App\FeedManagement\Application\UseCase\Query\GetBookmarkInfosList;
use App\FeedManagement\Application\UseCase\QueryHandler\GetBookmarkInfosListHandler;
use App\FeedManagement\Domain\Model\Identity\BookmarkId;
use App\FeedManagement\Infrastructure\Persistence\Doctrine\CacheKey\BookmarkCacheKey;
use App\SharedKernel\Domain\Model\ValueObject\Pagination;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\Mapping;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\NoResult;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Connection;
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
            ->select('b.id AS bookmark_id, b.name AS bookmark_name, b.description AS bookmark_description')
            ->addSelect('b.created_at AS bookmark_created_at, b.updated_at AS bookmark_updated_at')
            ->addSelect('COUNT(ba.article_id) AS bookmark_articles_count, b.is_public AS bookmark_is_public')
            ->leftJoin('b', 'bookmark_article', 'ba', 'ba.bookmark_id = b.id')
            ->where('b.user_id = :userId')
            ->setParameter('userId', $query->userId->toBinary())
            ->orderBy('b.created_at', 'DESC')
            ->groupBy('b.id')
            ->enableResultCache(new QueryCacheProfile(0, BookmarkCacheKey::BOOKMARK_INFO_LIST->withId($query->userId->toString())))
        ;

        try {
            /** @var SlidingPaginationInterface<int, array<string, mixed>> $data */
            $data = $this->paginator->paginate($qb, $query->page->page, $query->page->limit);
        } catch (\Throwable $e) {
            throw NoResult::forQuery($qb->getSQL(), $qb->getParameters(), $e);
        }

        return $this->mapBookmarkInfosList($data);
    }

    /**
     * @param SlidingPaginationInterface<int, array<string, mixed>> $data
     */
    private function mapBookmarkInfosList(SlidingPaginationInterface $data): BookmarkInfosList
    {
        return new BookmarkInfosList(
            items: array_map(
                fn ($item): BookmarkInfos => $this->mapBookmarkInfos($item),
                \iterator_to_array($data->getItems())
            ),
            pagination: Pagination::create($data->getPaginationData())
        );
    }

    private function mapBookmarkInfos(array $item): BookmarkInfos
    {
        return new BookmarkInfos(
            BookmarkId::fromBinary($item['bookmark_id']),
            Mapping::string($item, 'bookmark_name'),
            Mapping::datetime($item, 'bookmark_created_at'),
            Mapping::nullableString($item, 'bookmark_description'),
            Mapping::integer($item, 'bookmark_articles_count'),
            Mapping::boolean($item, 'bookmark_is_public'),
            Mapping::nullableDatetime($item, 'bookmark_updated_at')
        );
    }
}
