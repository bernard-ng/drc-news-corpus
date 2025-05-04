<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\UseCase\CommandHandler;

use App\IdentityAndAccess\Application\UseCase\Command\LockAccount;
use App\IdentityAndAccess\Application\UseCase\Command\RegisterLoginAttempt;
use App\IdentityAndAccess\Domain\Model\Entity\LoginAttempt;
use App\IdentityAndAccess\Domain\Model\Repository\LoginAttemptRepository;
use App\IdentityAndAccess\Domain\Model\Repository\UserRepository;
use App\SharedKernel\Application\Messaging\CommandBus;
use App\SharedKernel\Application\Messaging\CommandHandler;

/**
 * Class RegisterLoginAttemptHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class RegisterLoginAttemptHandler implements CommandHandler
{
    private const int ATTEMPTS_LIMIT = 3;

    public function __construct(
        private UserRepository $userRepository,
        private LoginAttemptRepository $loginAttemptRepository,
        private CommandBus $commandBus
    ) {
    }

    public function __invoke(RegisterLoginAttempt $command): void
    {
        $user = $this->userRepository->getById($command->userId);
        $attempts = $this->loginAttemptRepository->countBy($user);

        if ($attempts < self::ATTEMPTS_LIMIT) {
            $this->loginAttemptRepository->add(LoginAttempt::create($user));
        } elseif ($user->isLocked === false) {
            $this->commandBus->handle(new LockAccount($user->id));
        }
    }
}
