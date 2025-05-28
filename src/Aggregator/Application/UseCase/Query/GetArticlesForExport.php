<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\Query;

use App\SharedKernel\Domain\Model\ValueObject\DateRange;

/**
 * Class GetArticlesForExport.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetArticlesForExport
{
    public function __construct(
        public ?string $source = null,
        public ?DateRange $date = null
    ) {
    }
}
