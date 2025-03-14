<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\UseCase\CommandHandler;

use App\IdentityAndAccess\Application\UseCase\Command\RequestPasswordReset;
use App\IdentityAndAccess\Domain\Exception\UserNotFound;
use App\IdentityAndAccess\Domain\Model\Repository\UserRepository;
use App\IdentityAndAccess\Domain\Service\SecretGenerator;
use App\SharedKernel\Domain\EventDispatcher\EventDispatcher;

/**
 * Class RequestPasswordResetHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class RequestPasswordResetHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private SecretGenerator $tokenGenerator,
        private EventDispatcher $eventDispatcher
    ) {
    }

    public function __invoke(RequestPasswordReset $command): void
    {
        $user = $this->userRepository->getByEmail($command->email);
        if ($user === null) {
            throw UserNotFound::withEmail($command->email);
        }

        $token = $this->tokenGenerator->generateToken(60);
        $user->passwordForgotten($token);

        $this->userRepository->add($user);
        $this->eventDispatcher->dispatch($user->releaseEvents());
    }
}
