<?php

declare(strict_types=1);

namespace App\Filter;

/**
 * Class PageRange.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class PageRange implements \Stringable
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

    public static function from(string $interval): self
    {
        [$start, $end] = explode(':', $interval);

        $start = (int) $start;
        $end = (int) $end;

        return new self($start, $end);
    }

    public function inRange(int $page): bool
    {
        return $page >= $this->start && $page <= $this->end;
    }
}
