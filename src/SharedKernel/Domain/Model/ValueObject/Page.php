<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Model\ValueObject;

use Webmozart\Assert\Assert;

/**
 * Class Page.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Page
{
    public const int DEFAULT_PAGE = 1;

    public const int DEFAULT_LIMIT = 10;

    public function __construct(
        public int $page = self::DEFAULT_PAGE,
        public int $limit = self::DEFAULT_LIMIT,
    ) {
        Assert::greaterThanEq($this->page, self::DEFAULT_PAGE);
        Assert::greaterThanEq($this->limit, self::DEFAULT_LIMIT);
    }
}
