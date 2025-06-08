<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Model\Pagination;

use App\SharedKernel\Domain\DataTransfert\DataMapping;
use Symfony\Component\Uid\UuidV7;

/**
 * Class PaginationCursor.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class PaginationCursor
{
    public function __construct(
        public UuidV7 $id,
        public \DateTimeImmutable $date,
    ) {
    }

    /**
     * Creates a new PaginationCursor from a DateTimeImmutable and a UuidV7.
     * @throws \JsonException When JSON encoding fails
     */
    public static function encode(array $item, PaginatorKeyset $keyset): string
    {
        $id = DataMapping::uuid($item, $keyset->id)->toString();

        if ($keyset->date !== null) {
            $date = DataMapping::dateTime($item, $keyset->date)->format('Y-m-d H:i:s');

            return base64_encode(
                json_encode([
                    'date' => $date,
                    'id' => $id,
                ], JSON_THROW_ON_ERROR)
            );
        }

        return base64_encode(
            json_encode([
                'id' => $id,
            ], JSON_THROW_ON_ERROR)
        );

    }

    /**
     * Decodes a cursor string into a PaginationCursor object.
     * Returns null if the cursor is invalid or cannot be decoded.
     */
    public static function decode(?string $cursor): ?self
    {
        if ($cursor === null) {
            return null;
        }

        try {
            $data = json_decode(base64_decode($cursor), true, 512, JSON_THROW_ON_ERROR);

            if (! is_array($data) || ! isset($data['date'], $data['id'])) {
                throw new \InvalidArgumentException('Invalid cursor format');
            }

            return new self(
                id: UuidV7::fromString($data['id']),
                date: new \DateTimeImmutable($data['date'])
            );
        } catch (\Throwable) {
            return null;
        }
    }
}
