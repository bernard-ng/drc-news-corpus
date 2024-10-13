<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\Query;

/**
 * Class GetLatestPublicationDateQuery.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetLatestPublicationDateQuery
{
    public function __construct(
        public string $source,
        public ?string $category = null,
    ) {
    }
}
