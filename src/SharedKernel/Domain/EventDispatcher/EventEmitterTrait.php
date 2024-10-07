<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\EventDispatcher;

/**
 * Trait EventEmitterTrait.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
trait EventEmitterTrait
{
    /**
     * @var array<int, object>
     */
    private array $emittedEvents;

    public function emitEvent(object $event): void
    {
        $this->emittedEvents[] = $event;
    }

    /**
     * @return array<int, object>
     */
    public function emitEvents(): array
    {
        return $this->emittedEvents;
    }
}
