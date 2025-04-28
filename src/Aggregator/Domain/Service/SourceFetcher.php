<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Service;

use App\Aggregator\Domain\Model\ValueObject\DateRange;
use App\Aggregator\Domain\Model\ValueObject\FetchConfig;

/**
 * Interface SourceFetcher.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface SourceFetcher
{
    public function fetch(FetchConfig $config): void;

    public function fetchOne(string $html, ?DateRange $dateRange = null): void;

    public function supports(string $source): bool;
}
