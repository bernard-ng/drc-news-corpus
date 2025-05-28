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
        private(set) ?string $lastId = null,
    ) {
    }

    public function setLastId(?string $lastId): self
    {
        $this->lastId = $lastId;

        return $this;
    }

    public static function from(Page $page): self
    {
        return new self($page->page, $page->limit);
    }

    public static function create(array $data): self
    {
        Assert::notEmpty($data);
        Assert::keyExists($data, 'current');
        Assert::keyExists($data, 'numItemsPerPage');

        return new self(
            $data['current'],
            $data['numItemsPerPage'],
        );
    }

    public static function empty(): self
    {
        return new self(0, 0, null);
    }
}
