<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\Command;

use App\Aggregator\Domain\Model\ValueObject\Link;
use App\Aggregator\Domain\Service\Crawling\OpenGraph\OpenGraphObject;

/**
 * Class Save.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class CreateArticle
{
    public function __construct(
        public string $title,
        public Link $link,
        public string $categories,
        public string $body,
        public string $source,
        public int $timestamp,
        public ?OpenGraphObject $metadata = null
    ) {
    }
}
