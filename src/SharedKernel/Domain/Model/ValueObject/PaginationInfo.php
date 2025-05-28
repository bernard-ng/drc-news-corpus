<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Model\ValueObject;

use App\SharedKernel\Domain\Assert;

/**
 * Class PaginationInfo.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class PaginationInfo
{
    public function __construct(
        public readonly int $current,
        public readonly int $limit,
        public ?string $lastId = null,
    ) {
    }

    public static function from(Page $page): self
    {
        return new self($page->page, $page->limit, null);
    }

    public static function create(array $data): self
    {
        Assert::notEmpty($data);
        Assert::keyExists($data, 'current');
        Assert::keyExists($data, 'numItemsPerPage');

        return new self(
            $data['current'],
            $data['totalCount'],
        );
    }

    public static function empty(): self
    {
        return new self(0, 0, null);
    }
}
