<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Framework\Symfony\Messaging;

use App\SharedKernel\Application\Messaging\AsyncMessage;
use App\SharedKernel\Application\Messaging\MessageBus;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Class MessengerMessageBus.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class MessengerMessageBus implements MessageBus
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    #[\Override]
    public function dispatch(AsyncMessage $message): void
    {
        $this->messageBus->dispatch($message);
    }
}
