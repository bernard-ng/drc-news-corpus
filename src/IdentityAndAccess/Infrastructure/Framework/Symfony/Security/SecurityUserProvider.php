<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Infrastructure\Framework\Symfony\Security;

use App\IdentityAndAccess\Domain\Model\Entity\User;
use App\IdentityAndAccess\Domain\Model\Repository\UserRepository;
use App\SharedKernel\Domain\Model\ValueObject\EmailAddress;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class SecurityUserProvider.
 *
 * @implements UserProviderInterface<SecurityUser>
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class SecurityUserProvider implements UserProviderInterface
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    #[\Override]
    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    #[\Override]
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->userRepository->getByEmail(EmailAddress::from($identifier));
        if (! $user instanceof User) {
            throw new UserNotFoundException();
        }

        return SecurityUser::create($user);
    }

    #[\Override]
    public function supportsClass(string $class): bool
    {
        return $class === SecurityUser::class;
    }
}
