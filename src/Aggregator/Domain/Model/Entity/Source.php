<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Model\Entity;

use App\Aggregator\Domain\Model\Identity\SourceId;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Credibility;

/**
 * Class Source.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
class Source
{
    public readonly SourceId $id;

    public function __construct(
        public readonly string $name,
        public readonly string $url,
        private(set) Credibility $credibility = new Credibility(),
        private(set) ?string $displayName = null,
        private(set) ?string $description = null,
        private(set) ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->id = new SourceId();
    }

    public static function create(string $name, string $url): self
    {
        return new self($name, $url);
    }

    public function defineCredibility(Credibility $credibility): self
    {
        $this->credibility = $credibility;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function defineProfileInfos(?string $displayName, ?string $description): self
    {
        $this->displayName = $displayName;
        $this->description = $description;

        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }
}
