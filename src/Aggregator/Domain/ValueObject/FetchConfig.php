<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\ValueObject;

use Webmozart\Assert\Assert;

/**
 * Class FetchConfig.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class FetchConfig
{
    public function __construct(
        public string $id,
        public string $filename,
        public ?PageRange $page = null,
        public ?DateRange $date = null,
        public ?string $category = null
    ) {
        Assert::notEmpty($this->id);
        Assert::notEmpty($this->filename);
    }
}
