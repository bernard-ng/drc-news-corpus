<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Exception;

/**
 * Class DuplicatedArticle.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class DuplicatedArticle extends \DomainException
{
    public static function withLink(string $link): self
    {
        return new self(sprintf('duplicate article with %s link', $link));
    }
}
