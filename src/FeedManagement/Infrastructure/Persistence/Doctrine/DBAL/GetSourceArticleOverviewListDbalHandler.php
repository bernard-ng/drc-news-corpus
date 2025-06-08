<?php

declare(strict_types=1);

namespace App\FeedManagement\Infrastructure\Persistence\Doctrine\DBAL;

use App\FeedManagement\Application\ReadModel\ArticleOverviewList;
use App\FeedManagement\Application\UseCase\Query\GetSourceArticleOverviewList;
use App\FeedManagement\Application\UseCase\QueryHandler\GetSourceArticleOverviewListHandler;
use App\FeedManagement\Infrastructure\Persistence\Doctrine\DBAL\Queries\ArticleQuery;
use App\FeedManagement\Infrastructure\Persistence\Doctrine\DBAL\Queries\BookmarkQuery;
use App\FeedManagement\Infrastructure\Persistence\Doctrine\DBAL\Queries\SourceQuery;
use App\SharedKernel\Domain\Model\Pagination\PaginatorKeyset;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\Features\PaginationQuery;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\NoResult;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;

/**
 * Class GetArticleOverviewListDbalHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetSourceArticleOverviewListDbalHandler implements GetSourceArticleOverviewListHandler
{
    use PaginationQuery;
    use BookmarkQuery;
    use ArticleQuery;
    use SourceQuery;

    public function __construct(
        private Connection $connection,
    ) {
    }

    #[\Override]
    public function __invoke(GetSourceArticleOverviewList $query): ArticleOverviewList
    {
        $qb = $this->connection->createQueryBuilder();
        $qb = $this->addArticleOverviewSelectQuery($qb);
        $qb = $this->addSourceOverviewSelectQuery($qb);
        $qb = $this->addArticleBookmarkedExistsQuery($qb);

        $qb->from('article', 'a')
            ->innerJoin('a', 'source', 's', 'a.source_id = s.id')
            ->where('s.id = :sourceId')
            ->orderBy('a.published_at', $query->filters->sortDirection->value)
            ->setParameter('userId', $query->userId->toBinary(), ParameterType::BINARY)
            ->setParameter('sourceId', $query->sourceId->toBinary(), ParameterType::BINARY)
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
