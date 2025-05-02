<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL\Types;

use App\Aggregator\Domain\Model\ValueObject\Crawling\OpenGraph;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

/**
 * Class OpenGraphType.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class OpenGraphType extends Type
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getJsonTypeDeclarationSQL([
            'nullable' => true,
        ]);
    }

    public function getName(): string
    {
        return 'open_graph';
    }

    #[\Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?OpenGraph
    {
        if ($value === null) {
            return null;
        }

        if (! \is_string($value)) {
            throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', 'string', OpenGraph::class]);
        }

        try {
            return OpenGraph::tryFrom($value);
        } catch (\Throwable $e) {
            throw ConversionException::conversionFailed($value, $this->getName(), $e);
        }
    }

    #[\Override]
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value instanceof OpenGraph) {
            return json_encode($value) ?: null;
        }

        if ($value === null || $value === '') {
            return null;
        }

        if (! \is_string($value)) {
            throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', 'string', OpenGraph::class]);
        }

        throw ConversionException::conversionFailed($value, $this->getName());
    }
}
