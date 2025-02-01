<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Model;

/**
 * Interface SymfonyUuIdGenerator.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface IdGenerator
{
    public function uuid(): string;
}
