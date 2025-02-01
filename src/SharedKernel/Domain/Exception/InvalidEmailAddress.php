<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Exception;

/**
 * Class InvalidEmailAddress.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class InvalidEmailAddress extends \InvalidArgumentException implements UserFacingError
{
    public function __construct(
        private readonly string $email
    ) {
        parent::__construct(sprintf('%s : Invalid email address provided', $this->email));
    }

    public static function withValue(string $value): self
    {
        return new self($value);
    }

    #[\Override]
    public function translationId(): string
    {
        return 'shared_kernel.exceptions.invalid_email_address';
    }

    #[\Override]
    public function translationParameters(): array
    {
        return [
            '%email%' => $this->email,
        ];
    }

    #[\Override]
    public function translationDomain(): string
    {
        return 'messages';
    }
}
