<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Domain\Model\ValueObject;

use App\SharedKernel\Domain\Assert;

/**
 * @author bernard-ng <bernard@devscast.tech>
 */
readonly class Roles implements \Stringable
{
    private array $roles;

    public function __construct(array $roles = [Role::USER])
    {
        Assert::notEmpty($roles, 'identity_and_access.validations.empty_roles');
        Assert::allIsInstanceOf($roles, Role::class);

        $roles[] = Role::USER;
        $this->roles = array_unique(\array_map(fn (Role $role) => $role->value, $roles));
    }

    #[\Override]
    public function __toString(): string
    {
        return implode(',', $this->roles);
    }

    public static function admin(): self
    {
        return new self([Role::USER, Role::ADMIN]);
    }

    public static function user(): self
    {
        return new self();
    }

    public function toArray(): array
    {
        return $this->roles;
    }

    public static function fromArray(array $roles): self
    {
        return new self($roles);
    }

    public function contains(string $role): bool
    {
        return in_array($role, $this->roles, true);
    }
}
