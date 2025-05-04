<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Service\Crawling\OpenGraph;

/**
 * Class GraphElement.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
abstract class OpenGraphElement
{
    abstract public function supportedProperties(): array;

    public function getProperties(): array
    {
        return array_filter(
            array_map(
                fn (string $key, mixed $value): ?OpenGraphProperty => $value !== null ? new OpenGraphProperty($key, $value) : null,
                array_keys($this->supportedProperties()),
                array_values($this->supportedProperties())
            ),
        );
    }
}
