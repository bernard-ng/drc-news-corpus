<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Service\Crawling\OpenGraph\Elements;

use App\Aggregator\Domain\Service\Crawling\OpenGraph\OpenGraphElement;
use App\Aggregator\Domain\Service\Crawling\OpenGraph\OpenGraphProperty;

/**
 * Class Video.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Video extends OpenGraphElement
{
    public function __construct(
        public ?string $url = null,
        public ?string $secureUrl = null,
        public ?string $type = null,
        public ?int $width = null,
        public ?int $height = null
    ) {
    }

    public function supportedProperties(): array
    {
        return [
            OpenGraphProperty::VIDEO_URL => $this->url,
            OpenGraphProperty::VIDEO_SECURE_URL => $this->secureUrl,
            OpenGraphProperty::VIDEO_TYPE => $this->type,
            OpenGraphProperty::VIDEO_WIDTH => $this->width,
            OpenGraphProperty::VIDEO_HEIGHT => $this->height,
        ];
    }
}
