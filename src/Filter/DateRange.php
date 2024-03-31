<?php

declare(strict_types=1);

namespace App\Filter;

use DateTime;

/**
 * Class DateRange.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class DateRange implements \Stringable
{
    public int $start;

    public int $end;

    private function __construct(int $start, int $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function __toString(): string
    {
        return $this->start . ':' . $this->end;
    }

    public static function from(string $interval, string $format = 'Y-m-d'): self
    {
        [$startDate, $endDate] = explode(':', $interval);

        /** @var DateTime $start */
        $start = DateTime::createFromFormat($format, $startDate);

        /** @var DateTime $end */
        $end = DateTime::createFromFormat($format, $endDate);

        return new self((int) $start->format('U'), (int) $end->format('U'));
    }

    public function inRange(int $date): bool
    {
        return $date >= $this->start && $date <= $this->end;
    }
}
