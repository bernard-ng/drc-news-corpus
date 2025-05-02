<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Model\ValueObject\Crawling;

/**
 * Class OpenGraphMeta.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class OpenGraph implements \JsonSerializable
{
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $image = null,
        public ?string $video = null,
        public ?string $audio = null,
        public ?string $locale = null,
    ) {
    }

    public static function tryFrom(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }

        try {
            $object = \json_decode($value, true, 512, JSON_THROW_ON_ERROR);

            return new self(
                $object['title'] ?? null,
                $object['description'] ?? null,
                $object['image'] ?? null,
                $object['video'] ?? null,
                $object['audio'] ?? null,
                $object['locale'] ?? null,
            );
        } catch (\Throwable) {
            return null;
        }
    }

    #[\Override]
    public function jsonSerialize(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
            'video' => $this->video,
            'audio' => $this->audio,
            'locale' => $this->locale,
        ];
    }
}
