<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\UseCase\CommandHandler;

use App\IdentityAndAccess\Application\UseCase\Command\UpdatePassword;
use App\IdentityAndAccess\Domain\Model\Repository\UserRepository;
use App\IdentityAndAccess\Domain\Service\PasswordHasher;
use App\SharedKernel\Application\Messaging\CommandHandler;
use App\SharedKernel\Domain\EventDispatcher\EventDispatcher;

/**
 * Class UpdatePasswordHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class UpdatePasswordHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordHasher $passwordHasher,
        private EventDispatcher $eventDispatcher
    ) {
    }

    public function __invoke(UpdatePassword $command): void
    {
        $user = $this->userRepository->getById($command->userId);
        $user->updatePassword($command->current, $command->password, $this->passwordHasher);

        $this->userRepository->add($user);
        $this->eventDispatcher->dispatch($user->releaseEvents());
    }
}
