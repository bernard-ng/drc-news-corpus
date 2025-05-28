<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\ReadModel;

use App\Aggregator\Domain\Model\Identity\SourceId;
use App\SharedKernel\Domain\DataTransfert\DataMapping;

/**
 * Class SourceReference.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class SourceReference
{
    public function __construct(
        public SourceId $id,
        public string $name,
        public ?string $displayName,
        public ?string $image,
        public string $url
    ) {
    }

    public static function create(array $item): self
    {
        return new self(
            SourceId::fromBinary($item['source_id']),
            DataMapping::string($item, 'source_name'),
            DataMapping::nullableString($item, 'source_display_name'),
            DataMapping::nullableString($item, 'source_image'),
            DataMapping::string($item, 'source_url'),
        );
    }
}
