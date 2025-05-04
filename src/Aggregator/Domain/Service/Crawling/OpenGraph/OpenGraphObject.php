<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Service\Crawling\OpenGraph;

use App\Aggregator\Domain\Service\Crawling\OpenGraph\Elements\Audio;
use App\Aggregator\Domain\Service\Crawling\OpenGraph\Elements\Image;
use App\Aggregator\Domain\Service\Crawling\OpenGraph\Elements\Video;

/**
 * Class GraphObject.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
abstract class OpenGraphObject
{
    public function __construct(
        public array $audios = [],
        public ?string $description = null,
        public ?string $determiner = null,
        public array $images = [],
        public ?string $locale = null,
        public array $localeAlternate = [],
        public ?bool $richAttachment = null,
        public array $seeAlso = [],
        public ?string $siteName = null,
        public ?string $title = null,
        public ?string $type = null,
        public ?\DateTimeImmutable $updatedTime = null,
        public ?string $url = null,
        public array $videos = []
    ) {
    }

    public function assignProperties(array $properties, bool $debug = false): void
    {
        foreach ($properties as $property) {
            $name = $property->key;
            $value = $property->value;

            switch ($name) {
                case OpenGraphProperty::AUDIO:
                case OpenGraphProperty::AUDIO_URL:
                    $this->audios[] = new Audio($value);
                    break;
                case OpenGraphProperty::AUDIO_SECURE_URL:
                case OpenGraphProperty::AUDIO_TYPE:
                    if ($this->audios !== []) {
                        $this->handleAudioAttribute($this->audios[\count($this->audios) - 1], $name, $value);
                    } elseif ($debug) {
                        throw new \UnexpectedValueException(
                            \sprintf(
                                "Found '%s' property but no audio was found before.",
                                $name
                            )
                        );
                    }

                    break;
                case OpenGraphProperty::DESCRIPTION:
                    if ($this->description === null) {
                        $this->description = $value;
                    }

                    break;
                case OpenGraphProperty::DETERMINER:
                    if ($this->determiner === null) {
                        $this->determiner = $value;
                    }

                    break;
                case OpenGraphProperty::IMAGE:
                case OpenGraphProperty::IMAGE_URL:
                    $this->images[] = new Image($value);
                    break;
                case OpenGraphProperty::IMAGE_HEIGHT:
                case OpenGraphProperty::IMAGE_SECURE_URL:
                case OpenGraphProperty::IMAGE_TYPE:
                case OpenGraphProperty::IMAGE_WIDTH:
                case OpenGraphProperty::IMAGE_USER_GENERATED:
                    if ($this->images !== []) {
                        $this->handleImageAttribute($this->images[\count($this->images) - 1], $name, $value);
                    } elseif ($debug) {
                        throw new \UnexpectedValueException(
                            \sprintf(
                                "Found '%s' property but no image was found before.",
                                $name
                            )
                        );
                    }

                    break;
                case OpenGraphProperty::LOCALE:
                    if ($this->locale === null) {
                        $this->locale = $value;
                    }

                    break;
                case OpenGraphProperty::LOCALE_ALTERNATE:
                    $this->localeAlternate[] = $value;
                    break;
                case OpenGraphProperty::RICH_ATTACHMENT:
                    $this->richAttachment = $this->convertToBoolean($value);
                    break;
                case OpenGraphProperty::SEE_ALSO:
                    $this->seeAlso[] = $value;
                    break;
                case OpenGraphProperty::SITE_NAME:
                    if ($this->siteName === null) {
                        $this->siteName = $value;
                    }

                    break;
                case OpenGraphProperty::TITLE:
                    if ($this->title === null) {
                        $this->title = $value;
                    }

                    break;
                case OpenGraphProperty::UPDATED_TIME:
                    if (! $this->updatedTime instanceof \DateTimeImmutable) {
                        $this->updatedTime = $this->convertToDateTime($value);
                    }

                    break;
                case OpenGraphProperty::URL:
                    if ($this->url === null) {
                        $this->url = $value;
                    }

                    break;
                case OpenGraphProperty::VIDEO:
                case OpenGraphProperty::VIDEO_URL:
                    $this->videos[] = new Video($value);
                    break;
                case OpenGraphProperty::VIDEO_HEIGHT:
                case OpenGraphProperty::VIDEO_SECURE_URL:
                case OpenGraphProperty::VIDEO_TYPE:
                case OpenGraphProperty::VIDEO_WIDTH:
                    if ($this->videos !== []) {
                        $this->handleVideoAttribute($this->videos[\count($this->videos) - 1], $name, $value);
                    } elseif ($debug) {
                        throw new \UnexpectedValueException(\sprintf(
                            "Found '%s' property but no video was found before.",
                            $name
                        ));
                    }
            }
        }
    }

    public function getProperties(): array
    {
        $properties = [];

        foreach ($this->audios as $audio) {
            $properties = array_merge($properties, $audio->getProperties());
        }

        if ($this->title !== null) {
            $properties[] = new OpenGraphProperty(OpenGraphProperty::TITLE, $this->title);
        }

        if ($this->description !== null) {
            $properties[] = new OpenGraphProperty(OpenGraphProperty::DESCRIPTION, $this->description);
        }

        if ($this->determiner !== null) {
            $properties[] = new OpenGraphProperty(OpenGraphProperty::DETERMINER, $this->determiner);
        }

        foreach ($this->images as $image) {
            $properties = array_merge($properties, $image->getProperties());
        }

        if ($this->locale !== null) {
            $properties[] = new OpenGraphProperty(OpenGraphProperty::LOCALE, $this->locale);
        }

        foreach ($this->localeAlternate as $locale) {
            $properties[] = new OpenGraphProperty(OpenGraphProperty::LOCALE_ALTERNATE, $locale);
        }

        if ($this->richAttachment !== null) {
            $properties[] = new OpenGraphProperty(OpenGraphProperty::RICH_ATTACHMENT, (int) $this->richAttachment);
        }

        foreach ($this->seeAlso as $seeAlso) {
            $properties[] = new OpenGraphProperty(OpenGraphProperty::SEE_ALSO, $seeAlso);
        }

        if ($this->siteName !== null) {
            $properties[] = new OpenGraphProperty(OpenGraphProperty::SITE_NAME, $this->siteName);
        }

        if ($this->type !== null) {
            $properties[] = new OpenGraphProperty(OpenGraphProperty::TYPE, $this->type);
        }

        if ($this->updatedTime instanceof \DateTimeImmutable) {
            $properties[] = new OpenGraphProperty(OpenGraphProperty::UPDATED_TIME, $this->updatedTime->format('c'));
        }

        if ($this->url !== null) {
            $properties[] = new OpenGraphProperty(OpenGraphProperty::URL, $this->url);
        }

        foreach ($this->videos as $video) {
            $properties = array_merge($properties, $video->getProperties());
        }

        return $properties;
    }

    protected function convertToBoolean(string $value): bool
    {
        return match (strtolower($value)) {
            '1', 'true' => true,
            default => false,
        };
    }

    protected function convertToDateTime(string $value): ?\DateTimeImmutable
    {
        try {
            return new \DateTimeImmutable($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function handleAudioAttribute(Audio $element, string $name, string $value): void
    {
        switch ($name) {
            case OpenGraphProperty::AUDIO_TYPE:
                $element->type = $value;
                break;
            case OpenGraphProperty::AUDIO_SECURE_URL:
                $element->secureUrl = $value;
                break;
        }
    }

    private function handleImageAttribute(Image $element, string $name, string $value): void
    {
        switch ($name) {
            case OpenGraphProperty::IMAGE_HEIGHT:
                $element->height = (int) $value;
                break;
            case OpenGraphProperty::IMAGE_WIDTH:
                $element->width = (int) $value;
                break;
            case OpenGraphProperty::IMAGE_TYPE:
                $element->type = $value;
                break;
            case OpenGraphProperty::IMAGE_SECURE_URL:
                $element->secureUrl = $value;
                break;
            case OpenGraphProperty::IMAGE_USER_GENERATED:
                $element->userGenerated = $this->convertToBoolean($value);
                break;
        }
    }

    private function handleVideoAttribute(Video $element, string $name, string $value): void
    {
        switch ($name) {
            case OpenGraphProperty::VIDEO_HEIGHT:
                $element->height = (int) $value;
                break;
            case OpenGraphProperty::VIDEO_WIDTH:
                $element->width = (int) $value;
                break;
            case OpenGraphProperty::VIDEO_TYPE:
                $element->type = $value;
                break;
            case OpenGraphProperty::VIDEO_SECURE_URL:
                $element->secureUrl = $value;
                break;
        }
    }
}
