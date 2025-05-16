<?php

declare(strict_types=1);

namespace App\FeedManagement\Infrastructure\Persistence\Doctrine\DBAL;

use App\Aggregator\Domain\Model\Identity\ArticleId;
use App\Aggregator\Domain\Model\ValueObject\Crawling\OpenGraph;
use App\Aggregator\Domain\Model\ValueObject\Link;
use App\FeedManagement\Application\ReadModel\BookmarkedArticle;
use App\FeedManagement\Application\ReadModel\BookmarkedArticleList;
use App\FeedManagement\Application\UseCase\Query\GetBookmarkedArticleList;
use App\FeedManagement\Application\UseCase\QueryHandler\GetBookmarkedArticleListHandler;
use App\FeedManagement\Infrastructure\Persistence\Doctrine\CacheKey\BookmarkCacheKey;
use App\SharedKernel\Domain\Model\ValueObject\Pagination;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\Mapping;
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
            ->select('a.id AS article_id, a.title As article_title, a.link as article_link, LEFT(body, 200) AS article_excerpt')
            ->addSelect('a.published_at AS article_published_at, a.source AS article_source, metadata AS article_metadata')
            ->from('bookmark_article', 'ba')
            ->leftJoin('ba', 'article', 'a', 'a.id = ba.article_id')
            ->leftJoin('ba', 'bookmark', 'b', 'b.id = ba.bookmark_id')
            ->where('b.id = :bookmarkId AND b.user_id = :userId')
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

        return $this->mapBookmarkedArticleList($data);
    }

    /**
     * @param SlidingPaginationInterface<int, array<string, mixed>> $data
     */
    private function mapBookmarkedArticleList(SlidingPaginationInterface $data): BookmarkedArticleList
    {
        return new BookmarkedArticleList(
            items: array_map(
                fn ($item): BookmarkedArticle => $this->mapBookmarkedArticle($item),
                \iterator_to_array($data->getItems())
            ),
            pagination: Pagination::create($data->getPaginationData())
        );
    }

    private function mapBookmarkedArticle(array $data): BookmarkedArticle
    {
        $openGraph = OpenGraph::tryFrom(Mapping::nullableString($data, 'article_metadata'));

        return new BookmarkedArticle(
            ArticleId::fromBinary($data['article_id']),
            Mapping::string($data, 'article_title'),
            Link::from(Mapping::string($data, 'article_link')),
            Mapping::string($data, 'article_excerpt'),
            Mapping::string($data, 'article_source'),
            $openGraph?->image,
            Mapping::datetime($data, 'article_published_at')
        );
    }
}
