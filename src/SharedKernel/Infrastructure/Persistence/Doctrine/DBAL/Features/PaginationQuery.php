<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\Features;

use App\SharedKernel\Domain\Model\Pagination\Page;
use App\SharedKernel\Domain\Model\Pagination\PaginationCursor;
use App\SharedKernel\Domain\Model\Pagination\PaginationInfo;
use App\SharedKernel\Domain\Model\Pagination\PaginatorKeyset;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Provides methods for generating and applying pagination to datasets and query builders.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
trait PaginationQuery
{
    public function createPaginationInfo(array $data, Page $page, PaginatorKeyset $keyset): PaginationInfo
    {
        $paginationInfo = PaginationInfo::from($page);
        if ($data === []) {
            return $paginationInfo;
        }

        $paginationInfo->cursor = PaginationCursor::encode(array_pop($data), $keyset);

        return $paginationInfo;
    }

    public function applyCursorPagination(QueryBuilder $qb, Page $page, PaginatorKeyset $keyset): QueryBuilder
    {
        $cursor = PaginationCursor::decode($page->cursor);
        if (! $cursor instanceof PaginationCursor) {
            return $this->applyOffsetPagination($qb, $page);
        }

        if ($keyset->date === null) {
            $qb
                ->andWhere(sprintf('%s <= :cursorLastId', $keyset->id))
                ->setParameter('cursorLastId', $cursor->id->toString(), ParameterType::BINARY);
        } else {
            $qb
                ->andWhere(sprintf('(%s, %s) <= (:cursorLastDate, :cursorLastId)', $keyset->date, $keyset->id))
                ->setParameter('cursorLastDate', $cursor->id->toBinary(), ParameterType::BINARY)
                ->setParameter('cursorLastId', $cursor->date->format('Y-m-d H:i:s'));
        }

        return $qb->setMaxResults($page->limit + 1);
    }

    public function applyOffsetPagination(QueryBuilder $qb, Page $page): QueryBuilder
    {
        return $qb
            ->setFirstResult($page->offset)
            ->setMaxResults($page->limit)
        ;
    }
}
