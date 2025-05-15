<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\UseCase\CommandHandler;

use App\FeedManagement\Application\UseCase\Command\CreateBookmark;
use App\FeedManagement\Domain\Model\Entity\Bookmark;
use App\FeedManagement\Domain\Model\Repository\BookmarkRepository;
use App\IdentityAndAccess\Domain\Model\Repository\UserRepository;
use App\SharedKernel\Application\Messaging\CommandHandler;

/**
 * Class CreateBookHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class CreateBookHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private BookmarkRepository $bookmarkRepository
    ) {
    }

    public function __invoke(CreateBookmark $command): void
    {
        $user = $this->userRepository->getById($command->userId);
        $bookmark = Bookmark::create($user, $command->name, $command->description);

        $this->bookmarkRepository->add($bookmark);
    }
}
