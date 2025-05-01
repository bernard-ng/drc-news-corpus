<?php

declare(strict_types=1);

namespace App\SharedKernel\Application\Messaging;

/**
 * Interface CommandBus.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface CommandBus
{
    public function handle(object $message): mixed;
}
