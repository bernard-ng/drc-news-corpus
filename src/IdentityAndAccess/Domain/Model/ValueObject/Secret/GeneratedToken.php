<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Domain\Model\ValueObject\Secret;

use App\SharedKernel\Domain\Assert;

/**
 * Class GeneratedToken.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GeneratedToken implements \Stringable
{
    public function __construct(
        public string $token,
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
}
