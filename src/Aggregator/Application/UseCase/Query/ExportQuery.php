<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\Query;

use App\Aggregator\Domain\ValueObject\DateRange;

/**
 * Class ExportQuery.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class ExportQuery
{
    public function __construct(
        public ?string $source = null,
        public ?DateRange $date = null
    ) {
    }
}
