<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\EventListener;

use App\IdentityAndAccess\Application\Mailing\ConfirmationRequestedEmail;
use App\IdentityAndAccess\Domain\Event\ConfirmationRequested;
use App\IdentityAndAccess\Domain\Model\Repository\UserRepository;
use App\SharedKernel\Application\Mailing\Mailer;
use App\SharedKernel\Domain\EventListener\EventListener;

/**
 * Class UserRegisteredListener.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class ConfirmationRequestedListener implements EventListener
{
    public function __construct(
        private Mailer $mailer,
        private UserRepository $userRepository
    ) {
    }

    public function __invoke(ConfirmationRequested $event): void
    {
        $user = $this->userRepository->getById($event->userId);
        $email = new ConfirmationRequestedEmail($user->email, $user->name, $event->token);

        $this->mailer->send($email);
    }
}
