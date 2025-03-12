<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Framework\Symfony\EventDispatcher;

use App\SharedKernel\Domain\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class SymfonyEventDispatcher.
 *
 * @see https://symfony.com/doc/current/components/event_dispatcher.html
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class SymfonyEventDispatcher implements EventDispatcher
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * @param array<int, object> $events
     */
    #[\Override]
    public function dispatch(array $events): void
    {
        foreach ($events as $event) {
            $this->eventDispatcher->dispatch($event, $event::class);
        }
    }
}
