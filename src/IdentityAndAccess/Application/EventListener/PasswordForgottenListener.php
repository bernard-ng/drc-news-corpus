<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\EventListener;

use App\IdentityAndAccess\Application\Mailing\PasswordForgottenEmail;
use App\IdentityAndAccess\Domain\Event\PasswordForgotten;
use App\IdentityAndAccess\Domain\Model\Repository\UserRepository;
use App\SharedKernel\Application\Mailing\Mailer;
use App\SharedKernel\Domain\EventListener\EventListener;

/**
 * Class PasswordForgottenListener.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class PasswordForgottenListener implements EventListener
{
    public function __construct(
        private Mailer $mailer,
        private UserRepository $userRepository
    ) {
    }

    public function __invoke(PasswordForgotten $event): void
    {
        $user = $this->userRepository->getById($event->userId);
        $email = new PasswordForgottenEmail($user->email, $event->token);

        $this->mailer->send($email);
    }
}
