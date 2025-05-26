<?php

declare(strict_types=1);

namespace App\Aggregator\Application\ReadModel\Source;

use App\SharedKernel\Domain\DataTransfert\DataMapping;

/**
 * Class SourceReference.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class SourceReference
{
    public function __construct(
        public string $name,
        public ?string $displayName,
        public ?string $image,
        public string $url
    ) {
    }

    public static function create(array $item): self
    {
        return new self(
            DataMapping::string($item, 'source_name'),
            DataMapping::nullableString($item, 'source_display_name'),
            DataMapping::nullableString($item, 'source_image'),
            DataMapping::string($item, 'source_url'),
        );
    }
}
