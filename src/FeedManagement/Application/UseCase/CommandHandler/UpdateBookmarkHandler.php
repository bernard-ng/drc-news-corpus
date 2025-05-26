<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\UseCase\CommandHandler;

use App\FeedManagement\Application\UseCase\Command\UpdateBookmark;
use App\FeedManagement\Domain\Model\Repository\BookmarkRepository;
use App\IdentityAndAccess\Domain\Exception\PermissionNotGranted;
use App\SharedKernel\Application\Messaging\CommandHandler;

/**
 * Class UpdateBookmarkHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class UpdateBookmarkHandler implements CommandHandler
{
    public function __construct(
        private BookmarkRepository $bookmarkRepository
    ) {
    }

    public function __invoke(UpdateBookmark $command): void
    {
        $bookmark = $this->bookmarkRepository->getById($command->bookmarkId);
        if ($bookmark->user->id !== $command->userId) {
            throw PermissionNotGranted::withReason('feed_management.exceptions.cannot_update_bookmark');
        }

        $bookmark = match ($command->isPublic) {
            true => $bookmark->markAsPublic(),
            false => $bookmark->markAsPrivate(),
        };
        $bookmark->updateInfos($command->name, $command->description);

        $this->bookmarkRepository->add($bookmark);
    }
}
