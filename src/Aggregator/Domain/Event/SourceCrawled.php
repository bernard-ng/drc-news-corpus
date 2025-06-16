<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Event;

/**
 * Class SourceFetched.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class SourceCrawled
{
    public function __construct(
        public string $event,
        public string $source,
        public bool $notify = false
    ) {
    }
}
