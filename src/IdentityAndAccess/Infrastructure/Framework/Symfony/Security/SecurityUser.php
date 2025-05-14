<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Infrastructure\Framework\Symfony\Security;

use App\IdentityAndAccess\Domain\Model\Entity\User;
use App\IdentityAndAccess\Domain\Model\Identity\UserId;
use App\SharedKernel\Domain\Model\ValueObject\EmailAddress;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class SecurityUser.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class SecurityUser implements UserInterface, PasswordAuthenticatedUserInterface, EquatableInterface
{
    public function __construct(
        public UserId $userId,
        public EmailAddress $email,
        public ?string $password,
        public array $roles,
        public bool $isLocked,
        public bool $isConfirmed
    ) {
    }

    public static function create(User $user): self
    {
        return new self(
            $user->id,
            $user->email,
            (string) $user->password,
            $user->roles->toArray(),
            $user->isLocked,
            $user->isConfirmed
        );
    }

    #[\Override]
    public function getPassword(): ?string
    {
        return $this->password;
    }

    #[\Override]
    public function getRoles(): array
    {
        return $this->roles;
    }

    #[\Override]
    public function eraseCredentials(): void
    {
    }

    #[\Override]
    public function getUserIdentifier(): string
    {
        /** @var non-empty-string $email */
        $email = $this->email->value;

        return $email;
    }

    #[\Override]
    public function isEqualTo(UserInterface $user): bool
    {
        if (! $user instanceof self) {
            return false;
        }

        return $this->userId->equals($user->userId);
    }
}
