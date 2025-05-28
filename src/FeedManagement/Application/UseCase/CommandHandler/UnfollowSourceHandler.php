<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\UseCase\CommandHandler;

use App\FeedManagement\Application\UseCase\Command\UnfollowSource;
use App\FeedManagement\Domain\Exception\FollowedSourceNotFound;
use App\FeedManagement\Domain\Model\Entity\FollowedSource;
use App\FeedManagement\Domain\Model\Repository\FollowedSourceRepository;
use App\SharedKernel\Application\Messaging\CommandHandler;

/**
 * Class UnfollowSourceHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class UnfollowSourceHandler implements CommandHandler
{
    public function __construct(
        private FollowedSourceRepository $followedSourceRepository
    ) {
    }

    public function __invoke(UnfollowSource $command): void
    {
        $followedSource = $this->followedSourceRepository->getByUserId(
            $command->userId,
            $command->sourceId
        );

        if (! $followedSource instanceof FollowedSource) {
            throw FollowedSourceNotFound::with($command->userId, $command->sourceId);
        }

        $this->followedSourceRepository->remove($followedSource);
    }
}
