<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Model\ValueObject\Crawling;

/**
 * Class UpdateDirection.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
enum UpdateDirection: string
{
    case FORWARD = 'forward';
    case BACKWARD = 'backward';
}
