<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\EventListener;

use App\IdentityAndAccess\Application\Mailing\PasswordResetEmail;
use App\IdentityAndAccess\Domain\Event\PasswordReset;
use App\IdentityAndAccess\Domain\Model\Repository\UserRepository;
use App\SharedKernel\Application\Mailing\Mailer;
use App\SharedKernel\Domain\EventListener\EventListener;

/**
 * Class PasswordForgottenListener.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class PasswordResetListener implements EventListener
{
    public function __construct(
        private Mailer $mailer,
        private UserRepository $userRepository
    ) {
    }

    public function __invoke(PasswordReset $event): void
    {
        $user = $this->userRepository->getById($event->userId);
        $email = new PasswordResetEmail($user->email);

        $this->mailer->send($email);
    }
}
