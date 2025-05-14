<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\EventListener;

use App\IdentityAndAccess\Application\Mailing\AccountLockedEmail;
use App\IdentityAndAccess\Domain\Event\AccountLocked;
use App\IdentityAndAccess\Domain\Model\Repository\UserRepository;
use App\SharedKernel\Application\Mailing\Mailer;
use App\SharedKernel\Domain\EventListener\EventListener;

/**
 * Class AccountLockedListener.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class AccountLockedListener implements EventListener
{
    public function __construct(
        private Mailer $mailer,
        private UserRepository $userRepository
    ) {
    }

    public function __invoke(AccountLocked $event): void
    {
        $user = $this->userRepository->getById($event->userId);
        $email = new AccountLockedEmail($user->email, $event->token);

        $this->mailer->send($email);
    }
}
