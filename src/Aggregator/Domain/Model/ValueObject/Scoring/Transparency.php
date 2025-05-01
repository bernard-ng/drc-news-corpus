<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Model\ValueObject\Scoring;

/**
 * Enum Transparency.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
enum Transparency: string
{
    case HIGH = 'high';
    case MEDIUM = 'medium';
    case LOW = 'low';
}
