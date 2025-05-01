<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\Command;

/**
 * Class CreateSource.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class CreateSource
{
    public function __construct(
        public string $name
    ) {
    }
}
