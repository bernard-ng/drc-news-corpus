<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\UseCase\CommandHandler;

use App\IdentityAndAccess\Application\UseCase\Command\ConfirmAccount;
use App\IdentityAndAccess\Domain\Model\Repository\UserRepository;
use App\IdentityAndAccess\Domain\Model\Repository\VerificationTokenRepository;
use App\IdentityAndAccess\Domain\Model\ValueObject\TokenPurpose;
use App\SharedKernel\Application\Messaging\CommandHandler;
use App\SharedKernel\Domain\EventDispatcher\EventDispatcher;

/**
 * Class ConfirmRegistrationHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class ConfirmAccountHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private VerificationTokenRepository $verificationTokenRepository,
        private EventDispatcher $eventDispatcher
    ) {
    }

    public function __invoke(ConfirmAccount $command): void
    {
        $token = $this->verificationTokenRepository->getByToken(
            $command->token,
            TokenPurpose::CONFIRM_ACCOUNT
        );

        $user = $this->userRepository->getById($token->user->id);
        $user->confirmAccount();

        $this->verificationTokenRepository->remove($token);
        $this->eventDispatcher->dispatch($user->releaseEvents());
    }
}
