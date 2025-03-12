<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Model\ValueObject;

use App\SharedKernel\Domain\Assert;

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
        public int $totalPages
    ) {
    }

    public static function create(array $data): self
    {
        Assert::notEmpty($data);
        Assert::keyExists($data, 'current');
        Assert::keyExists($data, 'totalCount');
        Assert::keyExists($data, 'numItemsPerPage');
        Assert::keyExists($data, 'pageCount');

        return new self(
            $data['current'],
            $data['totalCount'],
            $data['numItemsPerPage'],
            $data['pageCount']
        );
    }

    public static function empty(): self
    {
        return new self(0, 0, 0, 0);
    }
}
