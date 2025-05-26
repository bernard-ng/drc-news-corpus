<?php

declare(strict_types=1);

namespace App\FeedManagement\Domain\Model\Entity;

use App\Aggregator\Domain\Model\Entity\Article;
use App\FeedManagement\Domain\Exception\ArticleAlreadyBookmarked;
use App\FeedManagement\Domain\Exception\BookmarkedArticleNotFound;
use App\FeedManagement\Domain\Model\Identity\BookmarkId;
use App\IdentityAndAccess\Domain\Model\Entity\User;
use App\SharedKernel\Domain\Model\Collection\DataCollection;

/**
 * Class Bookmark.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
class Bookmark
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

    public function updateInfos(string $name, ?string $description = null): self
    {
        $this->name = $name;
        $this->description = $description;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function markAsPrivate(): self
    {
        $this->isPublic = false;

        return $this;
    }

    public function markAsPublic(): self
    {
        $this->isPublic = true;

        return $this;
    }

    public function addArticle(Article $article): self
    {
        if ($this->articles->contains($article)) {
            throw ArticleAlreadyBookmarked::with($article->id, $this->id);
        }

        $this->articles->add($article);
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if (! $this->articles->contains($article)) {
            throw BookmarkedArticleNotFound::with($article->id, $this->id);
        }

        $this->articles->removeElement($article);

        return $this;
    }
}
