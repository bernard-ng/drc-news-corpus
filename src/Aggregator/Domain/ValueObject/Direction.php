<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\ValueObject;

/**
 * Class Direction.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
enum Direction: string
{
    case FORWARD = 'forward';
    case BACKWARD = 'backward';
}
