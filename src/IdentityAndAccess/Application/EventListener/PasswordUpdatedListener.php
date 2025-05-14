<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\EventListener;

use App\IdentityAndAccess\Application\Mailing\PasswordUpdatedEmail;
use App\IdentityAndAccess\Domain\Event\PasswordUpdated;
use App\IdentityAndAccess\Domain\Model\Repository\UserRepository;
use App\SharedKernel\Application\Mailing\Mailer;
use App\SharedKernel\Domain\EventListener\EventListener;

/**
 * Class PasswordUpdatedListener.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class PasswordUpdatedListener implements EventListener
{
    public function __construct(
        private Mailer $mailer,
        private UserRepository $userRepository
    ) {
    }

    public function __invoke(PasswordUpdated $event): void
    {
        $user = $this->userRepository->getById($event->userId);
        $email = new PasswordUpdatedEmail($user->email);

        $this->mailer->send($email);
    }
}
