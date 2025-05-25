<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Model\ValueObject;

/**
 * Class ReadingTime.
 *
 * The average reading rate is actually 238, but 200 is a nice compromise and is easier to remember.
 *
 * Here’s the formula:
 * Get your total word count (including the headline and subhead).
 * Divide total word count by 200. The number before the decimal is your minutes.
 * Take the decimal points and multiply that number by .60. That will give you your seconds.
 *
 * Example:
 * 783 words ÷ 200 = 3.915 (3 = 3 minutes)
 * .915 × .60 = .549 (a little over 54 seconds, so I’d bump it up to 60 seconds, or a full minute)
 * reading time for this example article is 4 minutes
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class ReadingTime implements \Stringable, \JsonSerializable
{
    public const int WORDS_PER_MINUTE = 200;

    public int $readingTime;

    public function __construct(
        string|int $value
    ) {
        $this->readingTime = is_string($value) ? intval(str_word_count($value) / self::WORDS_PER_MINUTE) : $value;
    }

    public function __toString(): string
    {
        return (string) $this->readingTime;
    }

    public static function create(?int $value): self
    {
        return new self($value ?? 1);
    }

    public static function fromContent(string $content): self
    {
        return new self($content);
    }

    public function jsonSerialize(): string
    {
        return (string) $this->readingTime;
    }
}
