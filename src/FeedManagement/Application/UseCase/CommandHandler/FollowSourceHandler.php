<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\UseCase\CommandHandler;

use App\Aggregator\Domain\Model\Repository\SourceRepository;
use App\FeedManagement\Application\UseCase\Command\FollowSource;
use App\FeedManagement\Domain\Exception\SourceAlreadyFollowed;
use App\FeedManagement\Domain\Model\Entity\FollowedSource;
use App\FeedManagement\Domain\Model\Repository\FollowedSourceRepository;
use App\IdentityAndAccess\Domain\Model\Repository\UserRepository;
use App\SharedKernel\Application\Messaging\CommandHandler;

/**
 * Class FollowSourceHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class FollowSourceHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private SourceRepository $sourceRepository,
        private FollowedSourceRepository $followedSourceRepository
    ) {
    }

    public function __invoke(FollowSource $command): void
    {
        $followedSource = $this->followedSourceRepository->getByUserId(
            $command->userId,
            $command->sourceId
        );

        if ($followedSource instanceof FollowedSource) {
            throw SourceAlreadyFollowed::with($command->userId, $command->sourceId);
        }

        $user = $this->userRepository->getById($command->userId);
        $source = $this->sourceRepository->getById($command->sourceId);

        $followedSource = new FollowedSource($source, $user);
        $this->followedSourceRepository->add($followedSource);
    }
}
