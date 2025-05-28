<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Model\Entity;

use App\Aggregator\Domain\Model\Identity\ArticleId;
use App\Aggregator\Domain\Model\ValueObject\Crawling\OpenGraph;
use App\Aggregator\Domain\Model\ValueObject\Link;
use App\Aggregator\Domain\Model\ValueObject\ReadingTime;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Credibility;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Sentiment;
use App\Aggregator\Domain\Service\Crawling\OpenGraph\OpenGraphObject;

/**
 * Class Article.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
class Article
{
    public readonly ArticleId $id;

    public function __construct(
        public readonly string $title,
        public readonly Link $link,
        public readonly string $body,
        public readonly string $hash,
        private(set) string $categories,
        public readonly Source $source,
        public readonly \DateTimeImmutable $publishedAt,
        public readonly \DateTimeImmutable $crawledAt = new \DateTimeImmutable(),
        private(set) Credibility $credibility = new Credibility(),
        private(set) Sentiment $sentiment = Sentiment::NEUTRAL,
        private(set) ?OpenGraph $metadata = null,
        private(set) ?ReadingTime $readingTime = null,
        private(set) ?\DateTimeImmutable $updatedAt = null,
        public readonly ?string $image = null,
        public readonly ?string $excerpt = null,
    ) {
        $this->id = new ArticleId();
    }

    public function defineCredibility(Credibility $credibility): self
    {
        $this->credibility = $credibility;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function defineSentiment(Sentiment $sentiment): self
    {
        $this->sentiment = $sentiment;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function assignCategories(string $categories): self
    {
        $this->categories = $categories;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function computeReadingTime(): self
    {
        $this->readingTime = ReadingTime::fromContent($this->body);
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function defineOpenGraph(?OpenGraphObject $object): self
    {
        if ($object instanceof OpenGraphObject) {
            $image = $object->images[0] ?? null;
            $video = $object->videos[0] ?? null;
            $audio = $object->audios[0] ?? null;

            $this->metadata = new OpenGraph(
                title: $object->title,
                description: $object->description,
                image: $image->url ?? $image?->secureUrl,
                video: $video->url ?? $video?->secureUrl,
                audio: $audio->url ?? $audio?->secureUrl,
                locale: $object->locale
            );
        }

        return $this;
    }
}
