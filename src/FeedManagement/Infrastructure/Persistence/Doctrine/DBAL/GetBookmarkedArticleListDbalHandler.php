<?php

declare(strict_types=1);

namespace App\FeedManagement\Infrastructure\Persistence\Doctrine\DBAL;

use App\FeedManagement\Application\ReadModel\ArticleOverviewList;
use App\FeedManagement\Application\UseCase\Query\GetBookmarkedArticleList;
use App\FeedManagement\Application\UseCase\QueryHandler\GetBookmarkedArticleListHandler;
use App\FeedManagement\Infrastructure\Persistence\Doctrine\DBAL\Queries\ArticleQuery;
use App\FeedManagement\Infrastructure\Persistence\Doctrine\DBAL\Queries\SourceQuery;
use App\SharedKernel\Domain\Model\Pagination\PaginatorKeyset;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\Features\PaginationQuery;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\NoResult;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;

/**
 * Class GetBookmarkedArticleListDbalHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetBookmarkedArticleListDbalHandler implements GetBookmarkedArticleListHandler
{
    use PaginationQuery;
    use ArticleQuery;
    use SourceQuery;

    public function __construct(
        private Connection $connection,
    ) {
    }

    public function __invoke(GetBookmarkedArticleList $query): ArticleOverviewList
    {
        $qb = $this->connection->createQueryBuilder();
        $qb = $this->addArticleOverviewSelectQuery($qb);
        $qb = $this->addSourceOverviewSelectQuery($qb);

        $qb
            ->addSelect('1 as article_is_bookmarked')
            ->from('bookmark_article', 'ba')
            ->innerJoin('ba', 'article', 'a', 'a.id = ba.article_id')
            ->innerJoin('ba', 'bookmark', 'b', 'b.id = ba.bookmark_id AND b.user_id = :userId')
            ->innerJoin('a', 'source', 's', 'a.source_id = s.id')
            ->where('b.id = :bookmarkId')
            ->setParameter('bookmarkId', $query->bookmarkId->toBinary(), ParameterType::BINARY)
            ->setParameter('userId', $query->userId->toBinary(), ParameterType::BINARY)
        ;

        $qb = $this->applyArticleFilters($qb, $query->filters);
        $qb = $this->applyCursorPagination($qb, $query->page, new PaginatorKeyset('a.id', 'a.published_at'));

        try {
            $data = $qb->executeQuery()->fetchAllAssociative();
        } catch (\Throwable $e) {
            throw NoResult::forQuery($qb->getSQL(), $qb->getParameters(), $e);
        }

        $pagination = $this->createPaginationInfo($data, $query->page, new PaginatorKeyset('article_id', 'article_published_at'));
        return ArticleOverviewList::create($data, $pagination);
    }
}
