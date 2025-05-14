<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\EventListener;

use App\IdentityAndAccess\Application\Mailing\AccountUnlockedEmail;
use App\IdentityAndAccess\Domain\Event\AccountUnlocked;
use App\IdentityAndAccess\Domain\Model\Repository\UserRepository;
use App\SharedKernel\Application\Mailing\Mailer;
use App\SharedKernel\Domain\EventListener\EventListener;

/**
 * Class AccountUnlockedListener.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class AccountUnlockedListener implements EventListener
{
    public function __construct(
        private Mailer $mailer,
        private UserRepository $userRepository
    ) {
    }

    public function __invoke(AccountUnlocked $event): void
    {
        $user = $this->userRepository->getById($event->userId);
        $email = new AccountUnlockedEmail($user->email);

        $this->mailer->send($email);
    }
}
