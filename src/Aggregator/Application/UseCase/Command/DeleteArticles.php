<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\Command;

/**
 * Class DeleteArticles.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class DeleteArticles
{
    public function __construct(
        public string $source,
        public ?string $category = null
    ) {
    }
}
