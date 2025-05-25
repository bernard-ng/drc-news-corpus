<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\ReadModel;

use App\IdentityAndAccess\Domain\Model\Identity\UserId;
use App\SharedKernel\Domain\Model\ValueObject\EmailAddress;

/**
 * Class UserProfile.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class UserProfile
{
    public function __construct(
        public UserId $id,
        public string $name,
        public EmailAddress $email,
        public ?\DateTimeImmutable $updatedAt = null,
        public \DateTimeImmutable $createdAt = new \DateTimeImmutable(),
    ) {
    }
}
