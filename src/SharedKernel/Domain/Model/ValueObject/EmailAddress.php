<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Model\ValueObject;

use App\SharedKernel\Domain\Assert;
use App\SharedKernel\Domain\Exception\InvalidEmailAddress;

/**
 * Class EmailAddress.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class EmailAddress implements \Stringable, \JsonSerializable
{
    public string $value;

    public function __construct(string $value)
    {
        try {
            Assert::notEmpty($value);
            Assert::email($value);
        } catch (\Throwable) {
            throw InvalidEmailAddress::withValue($value);
        }

        $this->value = $value;
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @throws InvalidEmailAddress
     */
    public static function from(string $value): self
    {
        return new self($value);
    }

    public function provider(): string
    {
        return substr($this->value, strpos($this->value, '@') + 1);
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}
