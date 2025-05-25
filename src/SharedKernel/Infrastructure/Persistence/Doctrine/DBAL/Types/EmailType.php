<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\Types;

use App\SharedKernel\Domain\Model\ValueObject\EmailAddress;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

/**
 * Class EmailType.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class EmailType extends Type
{
    public const string NAME = 'email';

    #[\Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL([
            'length' => 255,
        ]);
    }

    #[\Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?EmailAddress
    {
        if ($value === null) {
            return null;
        }

        if (! \is_string($value)) {
            throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', 'string', EmailAddress::class]);
        }

        try {
            return EmailAddress::from($value);
        } catch (\Throwable $e) {
            throw ConversionException::conversionFailed($value, $this->getName(), $e);
        }
    }

    #[\Override]
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value instanceof EmailAddress) {
            return (string) $value;
        }

        if ($value === null || $value === '') {
            return null;
        }

        if (! \is_string($value)) {
            throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', 'string', EmailAddress::class]);
        }

        try {
            return (string) EmailAddress::from($value);
        } catch (\Throwable $e) {
            throw ConversionException::conversionFailed($value, $this->getName(), $e);
        }
    }

    #[\Override]
    public function getName(): string
    {
        return self::NAME;
    }
}
