<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\UseCase\CommandHandler;

use App\IdentityAndAccess\Application\UseCase\Command\RegisterLoginSuccess;
use App\IdentityAndAccess\Domain\Model\Entity\LoginHistory;
use App\IdentityAndAccess\Domain\Model\Repository\LoginAttemptRepository;
use App\IdentityAndAccess\Domain\Model\Repository\LoginHistoryRepository;
use App\IdentityAndAccess\Domain\Model\Repository\UserRepository;
use App\SharedKernel\Application\Messaging\CommandHandler;
use App\SharedKernel\Domain\EventDispatcher\EventDispatcher;
use App\SharedKernel\Domain\Tracking\ClientProfiler;

/**
 * Class RegisterLoginSuccessHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class RegisterLoginSuccessHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private LoginHistoryRepository $loginHistoryRepository,
        private LoginAttemptRepository $loginAttemptRepository,
        private ClientProfiler $clientProfiler,
        private EventDispatcher $eventDispatcher
    ) {
    }

    public function __invoke(RegisterLoginSuccess $command): void
    {
        $user = $this->userRepository->getById($command->userId);
        $device = $this->clientProfiler->detect($command->profile);
        $location = $this->clientProfiler->locate($command->profile);

        $current = LoginHistory::create($user, $command->profile->userIp, $device, $location);
        $previous = $this->loginHistoryRepository->getLastBy($user);
        if ($previous instanceof LoginHistory) {
            $current->matchPreviousProfile($previous);
        }

        $this->loginHistoryRepository->add($current);
        $this->loginAttemptRepository->deleteBy($user);
        $this->eventDispatcher->dispatch($current->releaseEvents());
    }
}
