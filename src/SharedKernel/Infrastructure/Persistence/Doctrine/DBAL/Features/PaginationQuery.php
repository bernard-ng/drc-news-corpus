<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\Features;

use App\SharedKernel\Domain\DataTransfert\DataMapping;
use App\SharedKernel\Domain\Model\ValueObject\Page;
use App\SharedKernel\Domain\Model\ValueObject\Pagination;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Component\Uid\Uuid;

/**
 * Provides methods for generating and applying pagination to datasets and query builders.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
trait PaginationQuery
{
    /**
     * Generates pagination information based on the provided data and pagination settings.
     *
     * @param array $data The dataset to be paginated.
     * @param Page $page The Page instance containing pagination details.
     * @param non-empty-string $field The field name used to extract the last record's identifier.
     *
     * @return Pagination The Pagination instance with configured details.
     */
    public function getPagination(array $data, Page $page, string $field): Pagination
    {
        if ($data === []) {
            return Pagination::from($page);
        }

        $pagination = Pagination::from($page);
        $pagination->lastId = DataMapping::uuid(array_pop($data), $field)->toString();

        return $pagination;
    }

    /**
     * Applies cursor-based pagination to the given query builder.
     *
     * @param QueryBuilder $qb The query builder to apply pagination to.
     * @param Page $page The pagination information, including the last ID and limit.
     * @param string $field The field used for pagination comparison.
     * @param callable $getLastId A callback to retrieve the initial ID when the last ID is not set.
     *
     * @return QueryBuilder The modified query builder with pagination applied.
     */
    public function applyCursorPagination(QueryBuilder $qb, Page $page, string $field, callable $getLastId): QueryBuilder
    {
        if ($page->lastId === null) {
            $page->lastId = Uuid::fromBinary($getLastId())->toString();
        }

        return $qb
            ->andWhere($qb->expr()->lt($field, ':lastId'))
            ->setMaxResults($page->limit)
            ->setParameter('lastId', Uuid::fromString($page->lastId)->toBinary(), ParameterType::BINARY)
        ;
    }

    /**
     * Applies offset-based pagination to the given QueryBuilder instance.
     *
     * @param QueryBuilder $qb The QueryBuilder instance to apply pagination on.
     * @param Page $page The Page instance containing offset and limit information.
     *
     * @return QueryBuilder The modified QueryBuilder instance with pagination applied.
     */
    public function applyOffsetPagination(QueryBuilder $qb, Page $page): QueryBuilder
    {
        return $qb
            ->setFirstResult($page->offset)
            ->setMaxResults($page->limit)
        ;
    }
}
