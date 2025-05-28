<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\Command;

use App\SharedKernel\Domain\Model\ValueObject\DateRange;

/**
 * Class Export.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class ExportArticles
{
    public function __construct(
        public ?string $source = null,
        public ?DateRange $date = null
    ) {
    }
}
