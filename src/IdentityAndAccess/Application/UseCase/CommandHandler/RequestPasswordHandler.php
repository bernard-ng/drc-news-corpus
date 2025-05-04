<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\UseCase\CommandHandler;

use App\IdentityAndAccess\Application\UseCase\Command\RequestPassword;
use App\IdentityAndAccess\Domain\Exception\UserNotFound;
use App\IdentityAndAccess\Domain\Model\Entity\User;
use App\IdentityAndAccess\Domain\Model\Entity\VerificationToken;
use App\IdentityAndAccess\Domain\Model\Repository\UserRepository;
use App\IdentityAndAccess\Domain\Model\Repository\VerificationTokenRepository;
use App\IdentityAndAccess\Domain\Model\ValueObject\TokenPurpose;
use App\IdentityAndAccess\Domain\Service\SecretGenerator;
use App\SharedKernel\Application\Messaging\CommandHandler;
use App\SharedKernel\Domain\EventDispatcher\EventDispatcher;

/**
 * Class RequestPasswordHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class RequestPasswordHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private VerificationTokenRepository $verificationTokenRepository,
        private SecretGenerator $secretGenerator,
        private EventDispatcher $eventDispatcher,
    ) {
    }

    public function __invoke(RequestPassword $command): void
    {
        $user = $this->userRepository->getByEmail($command->email);
        if (! $user instanceof User) {
            throw UserNotFound::withEmail($command->email);
        }

        $token = $this->createVerificationToken($user);
        $user->requestPasswordReset($token);

        $this->userRepository->add($user);
        $this->verificationTokenRepository->add($token);
        $this->eventDispatcher->dispatch($user->releaseEvents());
    }

    private function createVerificationToken(User $user): VerificationToken
    {
        $secret = $this->secretGenerator->generateToken();
        return VerificationToken::create($user, $secret, TokenPurpose::PASSWORD_RESET);
    }
}
