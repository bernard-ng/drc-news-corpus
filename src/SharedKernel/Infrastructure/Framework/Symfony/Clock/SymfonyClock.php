<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Framework\Symfony\Clock;

use App\SharedKernel\Domain\Datetime\Clock;
use Psr\Clock\ClockInterface;

/**
 * Class SymfonyClock.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class SymfonyClock implements Clock
{
    public function __construct(
        private ClockInterface $clock
    ) {
    }

    #[\Override]
    public function now(): \DateTimeImmutable
    {
        return $this->clock->now();
    }
}
