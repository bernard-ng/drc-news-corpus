<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Framework\Symfony\Logging;

use Monolog\LogRecord;

/**
 * Class TelegramFormatter.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
class TelegramFormatter extends NormalizerFormatter
{
    public const int BATCH_MODE_JSON = 1;

    public const int BATCH_MODE_NEWLINES = 2;

    /**
     * @param self::BATCH_MODE_* $batchMode
     *
     * @throws \RuntimeException If the function json_encode does not exist
     */
    public function __construct(
        protected int $batchMode = self::BATCH_MODE_JSON,
        protected bool $appendNewline = true,
        protected bool $ignoreEmptyContextAndExtra = false,
        protected bool $includeStacktraces = false,
        string $basePath = ''
    ) {
        $this->basePath = $basePath;

        parent::__construct();
    }

    /**
     * The batch mode option configures the formatting style for
     * multiple records. By default, multiple records will be
     * formatted as a JSON-encoded array. However, for
     * compatibility with some API endpoints, alternative styles
     * are available.
     */
    public function getBatchMode(): int
    {
        return $this->batchMode;
    }

    /**
     * True if newlines are appended to every formatted record
     */
    public function isAppendingNewlines(): bool
    {
        return $this->appendNewline;
    }

    #[\Override]
    public function format(LogRecord $record): string
    {
        /** @var array<string, mixed> $normalized */
        $normalized = parent::format($record);

        if (isset($normalized['context']) && $normalized['context'] === []) {
            unset($normalized['context']);
        }

        if (isset($normalized['extra']) && $normalized['extra'] === []) {
            unset($normalized['extra']);
        }

        return $this->toJson($normalized, true) . "\n\n";
    }

    #[\Override]
    public function formatBatch(array $records): string
    {
        return match ($this->batchMode) {
            static::BATCH_MODE_NEWLINES => $this->formatBatchNewlines($records),
            default => $this->formatBatchJson($records),
        };
    }

    /**
     * @return $this
     */
    public function includeStacktraces(bool $include = true): self
    {
        $this->includeStacktraces = $include;

        return $this;
    }

    /**
     * Return a JSON-encoded array of records.
     *
     * @phpstan-param LogRecord[] $records
     */
    protected function formatBatchJson(array $records): string
    {
        return $this->toJson($this->normalize($records), true);
    }

    /**
     * Use new lines to separate records instead of a
     * JSON-encoded array.
     *
     * @phpstan-param LogRecord[] $records
     */
    protected function formatBatchNewlines(array $records): string
    {
        $oldNewline = $this->appendNewline;
        $this->appendNewline = false;
        $formatted = array_map(fn (LogRecord $record): string => $this->format($record), $records);
        $this->appendNewline = $oldNewline;

        return implode("\n\n", $formatted);
    }

    /**
     * Normalizes given $data.
     *
     * @return array<array|bool|float|int|object|string|null>|bool|float|int|object|string|null
     */
    #[\Override]
    protected function normalize(mixed $data, int $depth = 0): array|bool|float|int|object|string|null
    {
        if ($depth > $this->maxNormalizeDepth) {
            return 'Over ' . $this->maxNormalizeDepth . ' levels deep, aborting normalization';
        }

        if (\is_array($data)) {
            $normalized = [];

            $count = 1;
            foreach ($data as $key => $value) {
                if ($count++ > $this->maxNormalizeItemCount) {
                    $normalized['...'] = 'Over ' . $this->maxNormalizeItemCount . ' items (' . \count($data) . ' total), aborting normalization';
                    break;
                }

                $normalized[$key] = $this->normalize($value, $depth + 1);
            }

            return $normalized;
        }

        if (\is_object($data)) {
            if ($data instanceof \DateTimeInterface) {
                return $this->formatDate($data);
            }

            if ($data instanceof \Throwable) {
                /** @var array|float|object|bool|int|string|null $throwable */
                $throwable = $this->normalizeException($data, $depth);
                return $throwable;
            }

            // if the object has specific json serializability we want to make sure we skip the __toString treatment below
            if ($data instanceof \JsonSerializable) {
                return $data;
            }

            if ($data instanceof \Stringable) {
                return $data->__toString();
            }

            if ($data::class === '__PHP_Incomplete_Class') {
                return new \ArrayObject($data);
            }

            return $data;
        }

        if (\is_resource($data)) {
            return parent::normalize($data);
        }

        /** @var array|float|object|bool|int|string|null $data */
        return $data;
    }

    /**
     * Normalizes given exception with or without its own stack trace based on
     * `includeStacktraces` property.
     *
     * @inheritDoc
     */
    #[\Override]
    protected function normalizeException(\Throwable $e, int $depth = 0): array|float|object|bool|int|string|null
    {
        $data = parent::normalizeException($e, $depth);
        if (! $this->includeStacktraces) {
            unset($data['trace']);
        }

        return $data;
    }
}
