<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\Query;

/**
 * Class GetLatestPublicationDate.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetLatestPublicationDate
{
    public function __construct(
        public string $source,
        public ?string $category = null
    ) {
    }
}
