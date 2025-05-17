<?php

declare(strict_types=1);

namespace App\Aggregator\Application\ReadModel\Source;

/**
 * Class SourceReference.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class SourceReference
{
    public function __construct(
        public string $name,
        public ?string $displayName,
        public ?string $image,
        public string $url
    ) {
    }
}
