<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\UseCase\CommandHandler;

use App\Aggregator\Domain\Model\Repository\ArticleRepository;
use App\FeedManagement\Application\UseCase\Command\AddArticleToBookmark;
use App\FeedManagement\Domain\Model\Repository\BookmarkRepository;
use App\IdentityAndAccess\Domain\Exception\PermissionNotGranted;
use App\SharedKernel\Application\Messaging\CommandHandler;

/**
 * Class AddArticleToBookmarkHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class AddArticleToBookmarkHandler implements CommandHandler
{
    public function __construct(
        private BookmarkRepository $bookmarkRepository,
        private ArticleRepository $articleRepository
    ) {
    }

    public function __invoke(AddArticleToBookmark $command): void
    {
        $bookmark = $this->bookmarkRepository->getById($command->bookmarkId);
        if ($bookmark->user->id !== $command->userId) {
            throw PermissionNotGranted::withReason('feed_management.exceptions.cannot_add_article_to_bookmark');
        }

        $article = $this->articleRepository->getById($command->articleId);

        $bookmark->addArticle($article);
        $this->bookmarkRepository->add($bookmark);
    }
}
