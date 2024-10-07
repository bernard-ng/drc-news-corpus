<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Persistence;

use App\SharedKernel\Domain\Model\IdGenerator as IdGeneratorInterface;
use Symfony\Component\Uid\Uuid;

/**
 * Class IdGenerator.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class IdGenerator implements IdGeneratorInterface
{
    #[\Override]
    public function uuid(): string
    {
        return Uuid::v7()->toString();
    }
}
