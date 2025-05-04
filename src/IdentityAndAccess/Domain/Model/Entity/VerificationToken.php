<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Domain\Model\Entity;

use App\IdentityAndAccess\Domain\Exception\InvalidVerificationToken;
use App\IdentityAndAccess\Domain\Model\Identity\VerificationTokenId;
use App\IdentityAndAccess\Domain\Model\ValueObject\Secret\GeneratedToken;
use App\IdentityAndAccess\Domain\Model\ValueObject\TokenPurpose;

/**
 * Class VerificationToken.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
readonly class VerificationToken
{
    public const string DEFAULT_VALIDITY = 'PT2H';

    public VerificationTokenId $id;

    public function __construct(
        public User $user,
        public GeneratedToken $token,
        public TokenPurpose $purpose,
        public \DateTimeImmutable $createdAt = new \DateTimeImmutable()
    ) {
        $this->id = new VerificationTokenId();
    }

    public static function create(User $user, GeneratedToken $token, TokenPurpose $purpose): self
    {
        return new self($user, $token, $purpose);
    }

    public function isExpired(): bool
    {
        $now = new \DateTimeImmutable();
        $validUntil = (\DateTime::createFromImmutable($this->createdAt))
            ->add(new \DateInterval(self::DEFAULT_VALIDITY));

        return $now > $validUntil;
    }

    public function assertValid(): void
    {
        if ($this->isExpired()) {
            throw new InvalidVerificationToken();
        }
    }
}
