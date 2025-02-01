<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL;

/**
 * Class NoResult.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class NoResult extends \RuntimeException
{
    public static function forQuery(string $query, array $parameters, ?\Throwable $previous = null): self
    {
        return new self(
            sprintf('%s - Query "%s" (parameters: %s) produced no results', $previous?->getMessage(), $query, json_encode($parameters)),
            previous: $previous
        );
    }
}
