<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Datetime;

/**
 * Interface Clock.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface Clock
{
    public function now(): \DateTimeImmutable;
}
