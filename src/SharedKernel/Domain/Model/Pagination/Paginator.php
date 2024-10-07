<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Model\Pagination;

/**
 * Interface Paginator.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface Paginator
{
    public function paginate(mixed $target, int $page = 1, ?int $limit = null, array $options = []): Pagination;
}
