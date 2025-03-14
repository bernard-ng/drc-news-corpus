<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\UseCase\CommandHandler;

use App\IdentityAndAccess\Application\UseCase\Command\Register;
use App\IdentityAndAccess\Domain\Exception\EmailAlreadyUsed;
use App\IdentityAndAccess\Domain\Model\Entity\User;
use App\IdentityAndAccess\Domain\Model\Repository\UserRepository;
use App\IdentityAndAccess\Domain\Service\PasswordHasher;
use App\IdentityAndAccess\Domain\Service\SecretGenerator;
use App\SharedKernel\Application\Bus\CommandHandler;
use App\SharedKernel\Domain\EventDispatcher\EventDispatcher;

/**
 * Class RegisterHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class RegisterHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordHasher $passwordHasher,
        private SecretGenerator $tokenGenerator,
        private EventDispatcher $eventDispatcher
    ) {
    }

    public function __invoke(Register $command): void
    {
        $user = $this->userRepository->getByEmail($command->email);
        if ($user !== null) {
            throw EmailAlreadyUsed::with($command->email);
        }

        $user = User::register($command->name, $command->email, $command->roles);
        $password = $command->password ?? $this->tokenGenerator->generateCode();
        $user->definePassword($password, $this->passwordHasher);

        $this->userRepository->add($user);
        $this->eventDispatcher->dispatch($user->releaseEvents());
    }
}
