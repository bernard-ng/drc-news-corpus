<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\Query;

/**
 * Class GetEarliestPublicationDateQuery.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetEarliestPublicationDateQuery
{
    public function __construct(
        public string $source,
        public ?string $category = null
    ) {
    }
}
