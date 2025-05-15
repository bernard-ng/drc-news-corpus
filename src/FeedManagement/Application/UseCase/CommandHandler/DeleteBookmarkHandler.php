<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\UseCase\CommandHandler;

use App\FeedManagement\Application\UseCase\Command\DeleteBookmark;
use App\FeedManagement\Domain\Model\Repository\BookmarkRepository;
use App\IdentityAndAccess\Domain\Exception\PermissionNotGranted;
use App\SharedKernel\Application\Messaging\CommandHandler;

/**
 * Class DeleteBookmarkHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class DeleteBookmarkHandler implements CommandHandler
{
    public function __construct(
        private BookmarkRepository $bookmarkRepository,
    ) {
    }

    public function __invoke(DeleteBookmark $command): void
    {
        $bookmark = $this->bookmarkRepository->getById($command->bookmarkId);
        if ($bookmark->user->id !== $command->userId) {
            throw PermissionNotGranted::withReason('feed_management.exceptions.cannot_delete_bookmark');
        }

        $this->bookmarkRepository->remove($bookmark);
    }
}
