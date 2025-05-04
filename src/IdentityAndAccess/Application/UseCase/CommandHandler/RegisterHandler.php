<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\UseCase\CommandHandler;

use App\IdentityAndAccess\Application\UseCase\Command\Register;
use App\IdentityAndAccess\Domain\Exception\EmailAlreadyUsed;
use App\IdentityAndAccess\Domain\Model\Entity\User;
use App\IdentityAndAccess\Domain\Model\Entity\VerificationToken;
use App\IdentityAndAccess\Domain\Model\Repository\UserRepository;
use App\IdentityAndAccess\Domain\Model\Repository\VerificationTokenRepository;
use App\IdentityAndAccess\Domain\Model\ValueObject\TokenPurpose;
use App\IdentityAndAccess\Domain\Service\PasswordHasher;
use App\IdentityAndAccess\Domain\Service\SecretGenerator;
use App\SharedKernel\Application\Messaging\CommandHandler;
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
        private VerificationTokenRepository $verificationTokenRepository,
        private PasswordHasher $passwordHasher,
        private SecretGenerator $secretGenerator,
        private EventDispatcher $eventDispatcher
    ) {
    }

    public function __invoke(Register $command): void
    {
        $user = $this->userRepository->getByEmail($command->email);
        if ($user instanceof User) {
            throw EmailAlreadyUsed::with($command->email);
        }

        $user = User::register($command->name, $command->email, $command->roles);
        $password = $command->password ?? $this->secretGenerator->generateCode();
        $token = $this->createVerificationToken($user);

        $user
            ->definePassword($password, $this->passwordHasher)
            ->requestAccountConfirmation($token);

        $this->userRepository->add($user);
        $this->verificationTokenRepository->add($token);
        $this->eventDispatcher->dispatch($user->releaseEvents());
    }

    private function createVerificationToken(User $user): VerificationToken
    {
        $secret = $this->secretGenerator->generateToken();
        return VerificationToken::create($user, $secret, TokenPurpose::CONFIRM_ACCOUNT);
    }
}
