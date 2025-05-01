<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\Command;

/**
 * Class AddSource.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class AddSource
{
    public function __construct(
        public string $name
    ) {
    }
}
