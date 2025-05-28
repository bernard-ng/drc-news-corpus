<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Model\Entity;

use App\Aggregator\Domain\Model\Identity\CategoryId;

/**
 * Class Category.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
class Category
{
    public readonly CategoryId $id;

    public function __construct(
        public string $name,
        public string $slug,
        public array $children = [],
        public ?string $description = null,
        public ?string $image = null,
    ) {
        $this->id = new CategoryId();
    }
}
