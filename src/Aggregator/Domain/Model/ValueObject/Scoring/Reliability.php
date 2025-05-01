<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Model\ValueObject\Scoring;

/**
 * Class Reliability.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
enum Reliability: string
{
    case TRUSTED = 'trusted';
    case RELIABLE = 'reliable';
    case AVERAGE = 'average';
    case LOW_TRUST = 'low_trust';
    case UNRELIABLE = 'unreliable';
}
