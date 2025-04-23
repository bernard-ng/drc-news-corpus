<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Exception;

use App\Aggregator\Domain\Model\Entity\Identity\ArticleId;

/**
 * Class SourceNotFound.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class SourceNotFound extends \DomainException
{
    public static function withId(ArticleId $id): self
    {
        return new self(sprintf('article with id %s was not found', $id->toString()));
    }
}
