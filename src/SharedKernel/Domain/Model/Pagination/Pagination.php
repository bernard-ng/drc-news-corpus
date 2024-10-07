<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Model\Pagination;

/**
 * Interface Page.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface Pagination
{
    public function setCurrentPageNumber(int $pageNumber): void;

    public function getCurrentPageNumber(): int;

    public function setItemNumberPerPage(int $numItemsPerPage): void;

    public function getItemNumberPerPage(): int;

    public function setTotalItemCount(int $numTotal): void;

    public function getTotalItemCount(): int;

    public function setItems(iterable $items): void;

    public function getItems(): iterable;
}
