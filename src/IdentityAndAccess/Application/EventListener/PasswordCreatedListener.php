<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\EventListener;

use App\IdentityAndAccess\Application\Mailing\PasswordCreatedEmail;
use App\IdentityAndAccess\Domain\Event\PasswordCreated;
use App\IdentityAndAccess\Domain\Model\Repository\UserRepository;
use App\SharedKernel\Application\Mailing\Mailer;
use App\SharedKernel\Domain\EventListener\EventListener;

/**
 * Class PasswordCreatedListener.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class PasswordCreatedListener implements EventListener
{
    public function __construct(
        private Mailer $mailer,
        private UserRepository $userRepository
    ) {
    }

    public function __invoke(PasswordCreated $event): void
    {
        $user = $this->userRepository->getById($event->userId);
        $email = new PasswordCreatedEmail($user->email, $event->password);

        $this->mailer->send($email);
    }
}
