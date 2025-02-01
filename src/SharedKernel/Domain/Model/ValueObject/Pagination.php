<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Model\ValueObject;

/**
 * Class Pagination.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class Pagination
{
    public function __construct(
        public int $currentPage,
        public int $totalItems,
        public int $itemsPerPage,
        public int $totalPages,
        public array $data = [],
        public array $options = [],
        public array $parameters = [],
        public array $params = [],
        public ?string $route = null
    ) {
    }

    public static function create(array $data, ?array $options = [], ?array $parameters = [], array $params = [], ?string $route = null): self
    {
        return new self(
            $data['current'],
            $data['totalCount'],
            $data['numItemsPerPage'],
            $data['pageCount'],
            $data,
            $options ?? [],
            $parameters ?? [],
            $params,
            $route
        );
    }

    public static function empty(): self
    {
        return new self(0, 0, 0, 0);
    }

    public function isLastPage(): bool
    {
        return $this->currentPage === $this->totalPages;
    }
}
