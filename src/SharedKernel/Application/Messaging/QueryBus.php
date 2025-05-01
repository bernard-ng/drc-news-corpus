<?php

declare(strict_types=1);

namespace App\SharedKernel\Application\Messaging;

/**
 * Interface QueryBus.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface QueryBus
{
    public function handle(object $message): mixed;
}
