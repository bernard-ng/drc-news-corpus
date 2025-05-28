<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Exception;

use App\SharedKernel\Domain\Exception\UserFacingError;
use App\SharedKernel\Domain\Model\ValueObject\DateRange;

/**
 * Class ArticleOutOfRange.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class ArticleOutOfRange extends \DomainException implements UserFacingError
{
    public static function with(string $timestamp, DateRange $dateRange): self
    {
        $date = new \DateTimeImmutable('@' . $timestamp)
            ->format('Y-m-d H:i:s');
        $range = $dateRange->format('Y-m-d H:i:s');

        return new self(sprintf('article with timestamp %s is out of range %s', $date, $range));
    }

    public function translationId(): string
    {
        return 'aggregator.exceptions.article_out_of_range';
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
