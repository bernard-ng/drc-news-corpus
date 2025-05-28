<?php

declare(strict_types=1);

namespace Tests\Unit\Aggregator\Domain\Model\ValueObject;

use App\Aggregator\Domain\Model\ValueObject\Crawling\PageRange;
use App\SharedKernel\Domain\Exception\InvalidArgument;
use PHPUnit\Framework\TestCase;

/**
 * Class PageRangeTest.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class PageRangeTest extends TestCase
{
    public function testItShouldCreatePageRange(): void
    {
        $pageRange = PageRange::from('1:10');

        $this->assertEquals(1, $pageRange->start);
        $this->assertEquals(10, $pageRange->end);
    }

    public function testEndPageShouldBeGreaterThanStartPage(): void
    {
        $this->expectException(InvalidArgument::class);
        PageRange::from('10:1');
    }

    public function testNonNegativePages(): void
    {
        $this->expectException(InvalidArgument::class);
        PageRange::from('-1:-10');
    }
}
