<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Exception;

use App\Aggregator\Domain\Model\Entity\Identity\ArticleId;
use App\SharedKernel\Domain\Exception\UserFacingError;

/**
 * Class SourceNotFound.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class SourceNotFound extends \DomainException implements UserFacingError
{
    public static function withId(ArticleId $id): self
    {
        return new self(sprintf('article with id %s was not found', $id->toString()));
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
