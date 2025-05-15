<?php

declare(strict_types=1);

namespace App\FeedManagement\Domain\Model\Entity;

use App\Aggregator\Domain\Model\Entity\Article;
use App\FeedManagement\Domain\Model\Identity\BookmarkId;
use App\IdentityAndAccess\Domain\Model\Entity\User;
use App\SharedKernel\Domain\Model\Collection\DataCollection;

/**
 * Class Bookmark.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Bookmark
{
    public readonly BookmarkId $id;

    /**
     * @var DataCollection<int, Article>
     */
    private(set) public iterable $articles;

    private function __construct(
        public readonly User $user,
        private(set) string $name,
        private(set) ?string $description,
        private(set) bool $isPublic = false,
        private(set) ?\DateTimeImmutable $updatedAt = null,
        public readonly \DateTimeImmutable $createdAt = new \DateTimeImmutable()
    ) {
        $this->id = new BookmarkId();
        $this->articles = new DataCollection();
    }

    public static function create(User $user, string $name, ?string $description = null): self
    {
        return new self($user, $name, $description);
    }

    public function updateInfos(string $name, ?string $description = null, bool $isPublic = false): self
    {
        $this->name = $name;
        $this->description = $description;
        $this->isPublic = $isPublic;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function toggleVisibility(): self
    {
        $this->isPublic = ! $this->isPublic;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function addArticle(Article $article): self
    {
        if (! $this->articles->contains($article)) {
            $this->articles->add($article);
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        $this->articles->removeElement($article);

        return $this;
    }
}
