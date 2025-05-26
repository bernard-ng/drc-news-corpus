<?php

declare(strict_types=1);

namespace App\FeedManagement\Domain\Model\Entity;

use App\Aggregator\Domain\Model\Entity\Article;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Sentiment;
use App\FeedManagement\Domain\Model\Identity\CommentId;
use App\IdentityAndAccess\Domain\Model\Entity\User;

/**
 * Class Comment.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
class Comment
{
    public readonly CommentId $id;

    public function __construct(
        public readonly User $user,
        public readonly Article $article,
        public readonly string $content,
        private(set) bool $isSpam = false,
        private(set) Sentiment $sentiment = Sentiment::NEUTRAL,
        public readonly \DateTimeImmutable $createdAt = new \DateTimeImmutable(),
    ) {
        $this->id = new CommentId();
    }

    public static function create(User $user, Article $article, string $content): self
    {
        return new self($user, $article, $content);
    }

    public function defineSentiment(Sentiment $sentiment): self
    {
        $this->sentiment = $sentiment;

        return $this;
    }

    public function markAsSpam(): self
    {
        $this->isSpam = true;

        return $this;
    }
}
