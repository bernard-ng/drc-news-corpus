<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Exception;

/**
 * Class DuplicateArticle.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class DuplicateArticle extends \DomainException
{
    public static function withLink(string $link): self
    {
        return new self(sprintf('duplicate article with %s link', $link));
    }
}
