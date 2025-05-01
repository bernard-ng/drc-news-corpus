<?php

declare(strict_types=1);

namespace App\SharedKernel\Application\Messaging;

/**
 * Interface MessageBus.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface MessageBus
{
    public function dispatch(AsyncMessage $message): void;
}
