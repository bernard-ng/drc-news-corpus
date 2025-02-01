<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Model\ValueObject;

use App\SharedKernel\Domain\Exception\InvalidEmailAddress;

/**
 * Class Email.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class Email implements \Stringable
{
    public string $value;

    public function __construct(string $value)
    {
        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
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
}
