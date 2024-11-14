<?php

declare(strict_types=1);

namespace Tests\Unit\Aggregator\Domain\ValueObject;

use App\Aggregator\Domain\ValueObject\PageRange;
use PHPUnit\Framework\TestCase;

final class PageRangeTest extends TestCase
{
    public function testItShouldCreatePageRange(): void
    {
        $pageRange = PageRange::from('1:10');

        $this->assertInstanceOf(PageRange::class, $pageRange);
        $this->assertEquals(1, $pageRange->start);
        $this->assertEquals(10, $pageRange->end);
    }

    public function testEndPageShouldBeGreaterThanStartPage(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        PageRange::from('10:1');
    }

    public function testNonNegativePages(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        PageRange::from('-1:-10');
    }
}
