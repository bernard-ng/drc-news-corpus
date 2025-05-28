<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\Command;

use App\Aggregator\Domain\Model\ValueObject\Scoring\Credibility;

/**
 * Class CreateSource.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class CreateSource
{
    public function __construct(
        public string $name,
        public Credibility $credibility,
        public ?string $displayName = null,
        public ?string $description = null
    ) {
    }
}
