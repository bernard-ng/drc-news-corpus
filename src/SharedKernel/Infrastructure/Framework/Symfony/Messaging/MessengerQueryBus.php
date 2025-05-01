<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Framework\Symfony\Messaging;

use App\SharedKernel\Application\Messaging\QueryBus;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Class MessengerQueryBus.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class MessengerQueryBus implements QueryBus
{
    use HandleTrait {
        HandleTrait::handle as messengerHandle;
    }

    public function __construct(MessageBusInterface $queryBus)
    {
        $this->messageBus = $queryBus;
    }

    /**
     * @throws \Throwable
     */
    #[\Override]
    public function handle(object $message): mixed
    {
        try {
            return $this->messengerHandle($message);
        } catch (HandlerFailedException $e) {
            while ($e instanceof HandlerFailedException) {
                /** @var \Throwable $e */
                $e = $e->getPrevious();
            }

            throw $e;
        }
    }
}
