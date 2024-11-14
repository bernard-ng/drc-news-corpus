<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Service;

use App\Aggregator\Domain\Entity\Article;

/**
 * Interface Exporter.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface Exporter
{
    /**
     * @param iterable<Article> $data
     */
    public function export(iterable $data): string;
}
