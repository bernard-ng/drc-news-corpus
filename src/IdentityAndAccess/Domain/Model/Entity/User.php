<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Domain\Model\Entity;

use App\IdentityAndAccess\Domain\Model\Entity\Feature\PasswordFeature;
use App\IdentityAndAccess\Domain\Model\Entity\Identity\UserId;
use App\IdentityAndAccess\Domain\Model\ValueObject\Roles;
use App\IdentityAndAccess\Domain\Model\ValueObject\Secret\TimedToken;
use App\SharedKernel\Domain\Assert;
use App\SharedKernel\Domain\EventDispatcher\EventEmitterTrait;
use App\SharedKernel\Domain\Model\ValueObject\Email;

/**
 * Class User.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
class User
{
    use EventEmitterTrait;
    use PasswordFeature;

    public const string RESET_PASSWORD_VALIDITY = '+2 hours';

    public readonly UserId $id;
    private ?string $password = null;
    private ?TimedToken $passwordResetToken = null;
    private ?\DateTimeImmutable $updatedAt = null;

    private function __construct(
        private string $name,
        private Email $email,
        private Roles $roles,
        private ?\DateTimeImmutable $createdAt = new \DateTimeImmutable()
    ) {
        $this->id = new UserId();
    }

    public static function register(string $name, Email $email, ?Roles $roles): self
    {
        return new self($name, $email, $roles ?? Roles::user());
    }

    public function updateProfile(string $name, Email $email, Roles $roles): static
    {
        $this->name = $name;
        $this->email = $email;
        $this->roles = $roles;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function getRoles(): Roles
    {
        return $this->roles;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getPasswordResetToken(): ?TimedToken
    {
        return $this->passwordResetToken;
    }
}
