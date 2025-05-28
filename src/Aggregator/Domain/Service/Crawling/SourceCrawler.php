<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Service\Crawling;

use App\Aggregator\Domain\Model\ValueObject\Crawling\CrawlingSettings;
use App\SharedKernel\Domain\Model\ValueObject\DateRange;

/**
 * Interface SourceCrawler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface SourceCrawler
{
    public function fetch(CrawlingSettings $settings): void;

    public function fetchOne(string $html, ?DateRange $dateRange = null): void;

    public function supports(string $source): bool;
}
