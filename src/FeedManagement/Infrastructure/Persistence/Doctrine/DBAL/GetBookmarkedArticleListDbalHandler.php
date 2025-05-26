<?php

declare(strict_types=1);

namespace App\FeedManagement\Infrastructure\Persistence\Doctrine\DBAL;

use App\FeedManagement\Application\ReadModel\BookmarkedArticleList;
use App\FeedManagement\Application\UseCase\Query\GetBookmarkedArticleList;
use App\FeedManagement\Application\UseCase\QueryHandler\GetBookmarkedArticleListHandler;
use App\FeedManagement\Infrastructure\Persistence\Doctrine\CacheKey\BookmarkCacheKey;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\NoResult;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class GetBookmarkedArticleListDbalHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetBookmarkedArticleListDbalHandler implements GetBookmarkedArticleListHandler
{
    public function __construct(
        private Connection $connection,
        private PaginatorInterface $paginator
    ) {
    }

    public function __invoke(GetBookmarkedArticleList $query): BookmarkedArticleList
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'a.id as article_id',
                'a.title as article_title',
                'a.link as article_link',
                'a.categories as article_categories',
                'LEFT(a.body, 200) as article_excerpt',
                'a.published_at as article_published_at',
                "JSON_VALUE(a.metadata, '$.image') as article_image",
                'a.reading_time as article_reading_time',
            )
            ->addSelect(
                's.display_name as source_display_name',
                "CONCAT('https://devscast.org/images/sources/', s.name, '.png') as source_image",
                's.url as source_url',
                's.name as source_name',
            )
            ->from('bookmark_article', 'ba')
            ->innerJoin('ba', 'article', 'a', 'a.id = ba.article_id')
            ->innerJoin('ba', 'bookmark', 'b', 'b.id = ba.bookmark_id AND b.user_id = :userId')
            ->innerJoin('a', 'source', 's', 'a.source = s.name')
            ->where('b.id = :bookmarkId')
            ->setParameter('bookmarkId', $query->bookmarkId->toBinary(), ParameterType::BINARY)
            ->setParameter('userId', $query->userId->toBinary(), ParameterType::BINARY)
            ->enableResultCache(new QueryCacheProfile(0, BookmarkCacheKey::BOOKMARKED_ARTICLE_LIST->withId($query->bookmarkId->toString())))
        ;

        try {
            /** @var SlidingPaginationInterface<int, array<string, mixed>> $data */
            $data = $this->paginator->paginate($qb, $query->page->page, $query->page->limit);
        } catch (\Throwable $e) {
            throw NoResult::forQuery($qb->getSQL(), $qb->getParameters(), $e);
        }

        return BookmarkedArticleList::create($data->getItems(), $data->getPaginationData());
    }
}
