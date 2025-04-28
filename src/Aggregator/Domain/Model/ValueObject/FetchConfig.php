<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Model\ValueObject;

use App\SharedKernel\Domain\Assert;

/**
 * Class FetchConfig.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class FetchConfig
{
    public function __construct(
        public string $id,
        public ?PageRange $pageRange = null,
        public ?DateRange $dateRange = null,
        public ?string $category = null
    ) {
        Assert::notEmpty($this->id);
    }
}
