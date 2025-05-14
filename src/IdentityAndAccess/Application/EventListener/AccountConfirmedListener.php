<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\EventListener;

use App\IdentityAndAccess\Application\Mailing\AccountConfirmedEmail;
use App\IdentityAndAccess\Domain\Event\AccountConfirmed;
use App\IdentityAndAccess\Domain\Model\Repository\UserRepository;
use App\SharedKernel\Application\Mailing\Mailer;
use App\SharedKernel\Domain\EventListener\EventListener;

/**
 * Class AccountConfirmedListener.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class AccountConfirmedListener implements EventListener
{
    public function __construct(
        private Mailer $mailer,
        private UserRepository $userRepository
    ) {
    }

    public function __invoke(AccountConfirmed $event): void
    {
        $user = $this->userRepository->getById($event->userId);
        $email = new AccountConfirmedEmail($user->email, false, null);

        $this->mailer->send($email);
    }
}
