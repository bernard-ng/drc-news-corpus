<?php

declare(strict_types=1);

namespace App\Source;

use App\Filter\DateRange;
use App\Filter\PageRange;

/**
 * Class ProcessConfig.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class ProcessConfig
{
    public function __construct(
        public string $id,
        public string $filename,
        public ?PageRange $page = null,
        public ?DateRange $date = null,
        public ?string $category = null
    ) {
    }
}
