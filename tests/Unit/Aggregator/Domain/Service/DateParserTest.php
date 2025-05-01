<?php

declare(strict_types=1);

namespace Tests\Unit\Aggregator\Domain\Service;

use App\Aggregator\Domain\Service\Crawling\DateParser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Class DateParserTest.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class DateParserTest extends TestCase
{
    private DateParser $dateParser;

    #[\Override]
    protected function setUp(): void
    {
        $this->dateParser = new DateParser();
    }

    #[DataProvider('validDateProvider')]
    public function testCreateTimeStampWithValidDates(
        string $date,
        ?string $format,
        ?string $pattern,
        ?string $replacement,
        string $expected
    ): void {
        $result = $this->dateParser->createTimeStamp($date, $format, $pattern, $replacement);
        $this->assertEquals($expected, $result);
    }

    #[DataProvider('invalidDateProvider')]
    public function testCreateTimeStampWithInvalidDates(
        string $date,
        ?string $format,
        ?string $pattern,
        ?string $replacement
    ): void {
        $currentTimestamp = new \DateTime('midnight')->format('U');
        $result = $this->dateParser->createTimeStamp($date, $format, $pattern, $replacement);
        $this->assertEquals($currentTimestamp, $result);
    }

    public static function validDateProvider(): \Generator
    {
        yield ['2004-02-12T15:19:21', 'c', null, null, '1076599161'];
        yield ['08/10/2024 - 00:00', 'Y-m-d H:i', '/(\d{2})\/(\d{2})\/(\d{4}) - (\d{2}:\d{2})/', '$3-$2-$1 $4', '1728345600'];
        yield ['mar 08/10/2024 - 00:00', 'Y-m-d H:i', '/\w{3} (\d{2})\/(\d{2})\/(\d{4}) - (\d{2}:\d{2})/', '$3-$2-$1 $4', '1728345600'];
        yield ['Mardi 8 octobre 2024 - 00:00', 'Y-m-d H:i', '/(\d{1}) (\d{1,2}) (\d{2}) (\d{4}) - (\d{2}:\d{2})/', '$4-$3-$2 $5', '1728345600'];
        yield ['8.10.2024 00:00', 'd.m.Y H:i', null, null, '1728345600'];
    }

    public static function invalidDateProvider(): \Generator
    {
        yield ['invalid date string', null, null, null];
    }
}
