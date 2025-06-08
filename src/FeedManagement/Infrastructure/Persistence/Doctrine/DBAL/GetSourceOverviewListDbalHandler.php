<?php

declare(strict_types=1);

namespace App\FeedManagement\Infrastructure\Persistence\Doctrine\DBAL;

use App\FeedManagement\Application\ReadModel\SourceOverviewList;
use App\FeedManagement\Application\UseCase\Query\GetSourceOverviewList;
use App\FeedManagement\Application\UseCase\QueryHandler\GetSourceOverviewListHandler;
use App\FeedManagement\Infrastructure\Persistence\Doctrine\DBAL\Queries\SourceQuery;
use App\SharedKernel\Domain\Model\Pagination\PaginatorKeyset;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\Features\PaginationQuery;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\NoResult;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;

/**
 * Class GetSourceOverviewListDbalHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetSourceOverviewListDbalHandler implements GetSourceOverviewListHandler
{
    use PaginationQuery;
    use SourceQuery;

    public function __construct(
        private Connection $connection,
    ) {
    }

    #[\Override]
    public function __invoke(GetSourceOverviewList $query): SourceOverviewList
    {
        $qb = $this->connection->createQueryBuilder();
        $qb = $this->addSourceOverviewSelectQuery($qb);
        $qb = $this->addFollowedSourceExistsQuery($qb);

        $qb->from('source', 's')
            ->groupBy('s.name')
            ->setParameter('userId', $query->userId->toBinary(), ParameterType::BINARY)
        ;

        $qb = $this->applyCursorPagination($qb, $query->page, new PaginatorKeyset('s.id', 's.created_at'));

        try {
            $data = $qb->executeQuery()->fetchAllAssociative();
        } catch (\Throwable $e) {
            throw NoResult::forQuery($qb->getSQL(), $qb->getParameters(), $e);
        }

        $pagination = $this->createPaginationInfo($data, $query->page, new PaginatorKeyset('source_id'));
        return SourceOverviewList::create($data, $pagination);
    }
}
