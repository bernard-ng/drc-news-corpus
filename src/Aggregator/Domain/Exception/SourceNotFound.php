<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Exception;

use App\Aggregator\Domain\Model\Identity\SourceId;
use App\SharedKernel\Domain\Exception\UserFacingError;

/**
 * Class SourceNotFound.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class SourceNotFound extends \DomainException implements UserFacingError
{
    public static function withName(string $name): self
    {
        return new self(sprintf('source with name %s was not found', $name));
    }

    public static function withId(SourceId $sourceId): self
    {
        return new self(sprintf('source with id %s was not found', $sourceId->toString()));
    }

    public function translationId(): string
    {
        return 'aggregator.exceptions.source_not_found';
    }

    public function translationParameters(): array
    {
        return [];
    }

    public function translationDomain(): string
    {
        return 'aggregator';
    }
}
