<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\ReadModel;

use App\Aggregator\Domain\Model\Identity\SourceId;
use App\SharedKernel\Domain\DataTransfert\DataMapping;

/**
 * Class SourceOverview.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class SourceOverview
{
    public function __construct(
        public SourceId $id,
        public string $name,
        public string $url,
        public ?string $displayName = null,
        public bool $followed = false,
        public ?string $image = null
    ) {
    }

    public static function create(array $item): self
    {
        return new self(
            SourceId::fromBinary($item['source_id']),
            DataMapping::string($item, 'source_name'),
            DataMapping::string($item, 'source_url'),
            DataMapping::nullableString($item, 'source_display_name'),
            DataMapping::boolean($item, 'source_is_followed'),
            DataMapping::nullableString($item, 'source_image')
        );
    }
}
