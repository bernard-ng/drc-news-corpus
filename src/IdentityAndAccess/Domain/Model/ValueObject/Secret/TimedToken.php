<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Domain\Model\ValueObject\Secret;

use App\IdentityAndAccess\Domain\Exception\InvalidToken;
use App\SharedKernel\Domain\Assert;

/**
 * Class TimedToken.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class TimedToken implements \Stringable
{
    public const string DEFAULT_VALIDITY = 'P2H';

    public function __construct(
        public string $token,
        public \DateTimeImmutable $generatedAt = new \DateTimeImmutable(),
    ) {
        Assert::notEmpty($this->token);
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->token;
    }

    public function isEqualTo(self $token): bool
    {
        return $this->token === $token->token;
    }

    public function isExpired(): bool
    {
        $now = new \DateTimeImmutable();
        $validUntil = (\DateTime::createFromImmutable($this->generatedAt))
            ->add(new \DateInterval(self::DEFAULT_VALIDITY));

        return $now > $validUntil;
    }

    public function assertValidAgainst(?self $token): void
    {
        if (
            $token === null
            || $this->isEqualTo($token) === false
            || $this->isExpired()
        ) {
            throw new InvalidToken();
        }
    }
}
