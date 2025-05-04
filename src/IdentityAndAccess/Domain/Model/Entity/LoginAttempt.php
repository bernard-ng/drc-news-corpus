<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Domain\Model\Entity;

use App\IdentityAndAccess\Domain\Model\Identity\LoginAttemptId;

/**
 * Class LoginAttempt.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
readonly class LoginAttempt
{
    public LoginAttemptId $id;

    private function __construct(
        public User $user,
        public \DateTimeImmutable $createdAt = new \DateTimeImmutable()
    ) {
        $this->id = new LoginAttemptId();
    }

    public static function create(User $user): self
    {
        return new self($user);
    }
}
