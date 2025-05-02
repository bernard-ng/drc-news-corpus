<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Service\Crawling\OpenGraph\Elements;

use App\Aggregator\Domain\Service\Crawling\OpenGraph\OpenGraphElement;
use App\Aggregator\Domain\Service\Crawling\OpenGraph\OpenGraphProperty;

/**
 * Class Audio.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Audio extends OpenGraphElement
{
    public function __construct(
        public ?string $url = null,
        public ?string $secureUrl = null,
        public ?string $type = null
    ) {
    }

    public function supportedProperties(): array
    {
        return [
            OpenGraphProperty::AUDIO_URL => $this->url,
            OpenGraphProperty::AUDIO_SECURE_URL => $this->secureUrl,
            OpenGraphProperty::AUDIO_TYPE => $this->type,
        ];
    }
}
