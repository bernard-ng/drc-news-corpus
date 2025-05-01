<?php

declare(strict_types=1);

namespace Tests\Unit\Aggregator\Domain\Model\ValueObject;

use App\Aggregator\Domain\Model\ValueObject\Crawling\DateRange;
use App\SharedKernel\Domain\Exception\InvalidArgument;
use PHPUnit\Framework\TestCase;

/**
 * Class DateRangeTest.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class DateRangeTest extends TestCase
{
    public function testItShouldCreateDateRange(): void
    {
        $dateRange = DateRange::from(
            '2021-10-01 00:00:00--2021-10-10 00:00:00',
            'Y-m-d H:i:s',
            '--'
        );

        $this->assertInstanceOf(DateRange::class, $dateRange);
        $this->assertEquals(1633046400, $dateRange->start);
        $this->assertEquals(1633824000, $dateRange->end);
    }

    public function testEndShouldBeGreaterThanStart(): void
    {
        $this->expectException(InvalidArgument::class);
        DateRange::from('2021-10-10:2021-10-01');
    }
}
