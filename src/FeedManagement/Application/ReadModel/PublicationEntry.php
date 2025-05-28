<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\ReadModel;

/**
 * Class DallyEntry.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class PublicationEntry
{
    public function __construct(
        public string $date,
        public int $count
    ) {
    }
}
