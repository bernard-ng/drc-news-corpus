<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\Command;

/**
 * Class SaveArticle.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class SaveArticle
{
    public function __construct(
        public string $title,
        public string $link,
        public string $categories,
        public string $body,
        public string $source,
        public int $timestamp
    ) {
    }
}
