<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Model\Entity;

use App\Aggregator\Domain\Model\Entity\Identity\SourceId;

/**
 * Class Source.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
readonly class Source
{
    public readonly SourceId $id;

    public function __construct(
        private string $name,
        private string $url
    ) {
        $this->id = new SourceId();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
