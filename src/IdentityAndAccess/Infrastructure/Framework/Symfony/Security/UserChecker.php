<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Infrastructure\Framework\Symfony\Security;

use App\IdentityAndAccess\Domain\Exception\AccountIsLocked;
use App\IdentityAndAccess\Domain\Exception\AccountNotConfirmed;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserChecker.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class UserChecker implements UserCheckerInterface
{
    #[\Override]
    public function checkPreAuth(UserInterface $user): void
    {
        if ($user instanceof SecurityUser && $user->isLocked) {
            throw new AccountIsLocked();
        }
    }

    #[\Override]
    public function checkPostAuth(UserInterface $user): void
    {
        if ($user instanceof SecurityUser && $user->isConfirmed === false) {
            throw new AccountNotConfirmed();
        }
    }
}
