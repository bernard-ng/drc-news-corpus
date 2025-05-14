<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\EventListener;

use App\IdentityAndAccess\Application\Mailing\LoginProfileChangedEmail;
use App\IdentityAndAccess\Domain\Event\LoginProfileChanged;
use App\IdentityAndAccess\Domain\Model\Repository\UserRepository;
use App\SharedKernel\Application\Mailing\Mailer;
use App\SharedKernel\Domain\EventListener\EventListener;

/**
 * Class LoginProfileChangedListener.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class LoginProfileChangedListener implements EventListener
{
    public function __construct(
        private Mailer $mailer,
        private UserRepository $userRepository
    ) {
    }

    public function __invoke(LoginProfileChanged $event): void
    {
        $user = $this->userRepository->getById($event->userId);
        $email = new LoginProfileChangedEmail($user->email, $event->device, $event->location);

        $this->mailer->send($email);
    }
}
