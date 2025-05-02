<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Service\Crawling\OpenGraph\Elements;

use App\Aggregator\Domain\Service\Crawling\OpenGraph\OpenGraphElement;
use App\Aggregator\Domain\Service\Crawling\OpenGraph\OpenGraphProperty;

/**
 * Class Image.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Image extends OpenGraphElement
{
    public function __construct(
        public ?string $url = null,
        public ?string $secureUrl = null,
        public ?string $type = null,
        public ?int $width = null,
        public ?int $height = null,
        public ?bool $userGenerated = null
    ) {
    }

    public function supportedProperties(): array
    {
        return [
            OpenGraphProperty::IMAGE => $this->url,
            OpenGraphProperty::IMAGE_SECURE_URL => $this->secureUrl,
            OpenGraphProperty::IMAGE_TYPE => $this->type,
            OpenGraphProperty::IMAGE_WIDTH => $this->width,
            OpenGraphProperty::IMAGE_HEIGHT => $this->height,
            OpenGraphProperty::IMAGE_USER_GENERATED => $this->userGenerated,
        ];
    }
}
