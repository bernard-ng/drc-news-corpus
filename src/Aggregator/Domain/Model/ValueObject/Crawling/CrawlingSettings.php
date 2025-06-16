<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Model\ValueObject\Crawling;

use App\SharedKernel\Domain\Assert;
use App\SharedKernel\Domain\Model\ValueObject\DateRange;

/**
 * Class FetchConfig.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class CrawlingSettings
{
    public function __construct(
        public string $id,
        public ?PageRange $pageRange = null,
        public ?DateRange $dateRange = null,
        public ?string $category = null,
        public bool $notify = false
    ) {
        Assert::notEmpty($this->id);
    }
}
