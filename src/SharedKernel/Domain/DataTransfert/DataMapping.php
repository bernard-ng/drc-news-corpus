<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\DataTransfert;

use App\SharedKernel\Domain\Assert;
use BackedEnum as T;
use Symfony\Component\Uid\UuidV7;

/**
 * Class DataMapping.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
abstract class DataMapping
{
    /**
     * @param array<string, mixed> $data
     * @param non-empty-string $key
     */
    public static function uuid(array $data, string $key): UuidV7
    {
        Assert::keyExists($data, $key);
        return UuidV7::fromString($data[$key]);
    }

    /**
     * @template T of \BackedEnum
     * @param array<string, mixed> $data
     * @param non-empty-string $key
     * @param class-string<T> $class
     * @phpstan-return T
     */
    public static function enum(array $data, string $key, string $class): \BackedEnum
    {
        Assert::keyExists($data, $key);
        return $class::from($data[$key]);
    }

    /**
     * @template T of \BackedEnum
     * @param array<string, mixed> $data
     * @param non-empty-string $key
     * @param class-string<T> $class
     * @phpstan-return T
     */
    public static function nullableEnum(array $data, string $key, string $class): ?\BackedEnum
    {
        Assert::keyExists($data, $key);
        return $class::tryFrom($data[$key]);
    }

    /**
     * @param array<string, mixed> $data
     * @param non-empty-string $key
     */
    public static function string(array $data, string $key): string
    {
        if (! isset($data[$key]) || $data[$key] === '') {
            return '';
        }

        return strval($data[$key]);
    }

    /**
     * @param array<string, mixed> $data
     * @param non-empty-string $key
     */
    public static function nullableString(array $data, string $key): ?string
    {
        if (! isset($data[$key]) || $data[$key] === '') {
            return null;
        }

        return strval($data[$key]);
    }

    /**
     * @param array<string, mixed> $data
     * @param non-empty-string $key
     */
    public static function boolean(array $data, string $key): bool
    {
        return isset($data[$key]) && (bool) $data[$key];
    }

    /**
     * @param array<string, mixed> $data
     * @param non-empty-string $key
     */
    public static function nullableBoolean(array $data, string $key): ?bool
    {
        if (! isset($data[$key])) {
            return null;
        }

        return (bool) $data[$key];
    }

    /**
     * @param array<string, mixed> $data
     * @param non-empty-string $key
     */
    public static function integer(array $data, string $key): int
    {
        return isset($data[$key]) ? (int) $data[$key] : 0;
    }

    /**
     * @param array<string, mixed> $data
     * @param non-empty-string $key
     */
    public static function nullableInteger(array $data, string $key): ?int
    {
        if (! isset($data[$key])) {
            return null;
        }

        return (int) $data[$key];
    }

    /**
     * @param array<string, mixed> $data
     * @param non-empty-string $key
     */
    public static function float(array $data, string $key): float
    {
        return isset($data[$key]) ? (float) $data[$key] : 0.0;
    }

    /**
     * @param array<string, mixed> $data
     * @param non-empty-string $key
     */
    public static function nullableFloat(array $data, string $key): ?float
    {
        if (! isset($data[$key])) {
            return null;
        }

        return (float) $data[$key];
    }

    /**
     * @param array<string, mixed> $data
     * @param non-empty-string $key
     */
    public static function datetime(array $data, string $key, string $format = 'Y-m-d H:i:s'): \DateTimeImmutable
    {
        Assert::keyExists($data, $key);
        $datetime = \DateTimeImmutable::createFromFormat($format, $data[$key]);

        if ($datetime === false) {
            throw new \InvalidArgumentException('Invalid datetime format');
        }

        return $datetime;
    }

    /**
     * @param array<string, mixed> $data
     * @param non-empty-string $key
     */
    public static function nullableDatetime(array $data, string $key, string $format = 'Y-m-d H:i:s'): ?\DateTimeImmutable
    {
        if (! isset($data[$key])) {
            return null;
        }

        $datetime = \DateTimeImmutable::createFromFormat($format, $data[$key]);

        if ($datetime === false) {
            throw new \InvalidArgumentException('Invalid datetime format');
        }

        return $datetime;
    }
}
