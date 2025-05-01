<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Model\Entity;

use App\Aggregator\Domain\Model\ValueObject\Credibility\Credibility;

/**
 * Class Source.
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/3.3/reference/limitations-and-known-issues.html#join-columns-with-non-primary-keys
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
class Source
{
    public function __construct(
        public readonly string $name,
        public readonly string $url,
        private(set) Credibility $credibility = new Credibility(),
        private(set) ?\DateTimeImmutable $updatedAt = null
    ) {
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
}
