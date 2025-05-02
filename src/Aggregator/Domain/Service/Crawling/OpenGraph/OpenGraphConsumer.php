<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Service\Crawling\OpenGraph;

/**
 * Interface OpenGraphConsumer.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface OpenGraphConsumer
{
    public function consumeUrl(string $url): ?OpenGraphObject;

    public function consumeHtml(string $html, string $fallbackUrl): ?OpenGraphObject;
}
