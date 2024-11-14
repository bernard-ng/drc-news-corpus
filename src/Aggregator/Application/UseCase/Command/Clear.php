<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\Command;

/**
 * Class Clear.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class Clear
{
    public function __construct(
        public string $source,
        public ?string $category = null
    ) {
    }
}
