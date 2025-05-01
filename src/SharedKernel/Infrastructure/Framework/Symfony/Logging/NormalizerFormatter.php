<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Framework\Symfony\Logging;

use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\NormalizerFormatter as MonologNormalizerFormatter;
use Monolog\Utils;

/**
 * Class NormalizerFormatter.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
class NormalizerFormatter extends MonologNormalizerFormatter implements FormatterInterface
{
    public const string SIMPLE_DATE = 'Y-m-d\TH:i:sP';

    protected string $dateFormat;

    protected int $maxNormalizeDepth = 9;

    protected int $maxNormalizeItemCount = 1000;

    protected string $basePath = '';

    private int $jsonEncodeOptions = JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_PRETTY_PRINT;

    /**
     * Setting a base path will hide the base path from exception and stack trace file names to shorten them
     *
     * @return $this
     */
    #[\Override]
    public function setBasePath(string $path = ''): self
    {
        if ($path !== '') {
            $path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        }

        $this->basePath = $path;

        return $this;
    }

    /**
     * Return the JSON representation of a value
     *
     * @param mixed $data
     * @return string            if encoding fails and ignoreErrors is true 'null' is returned
     * @throws \RuntimeException if encoding fails and errors are not ignored
     */
    #[\Override]
    protected function toJson($data, bool $ignoreErrors = false): string
    {
        $json = Utils::jsonEncode($data, $this->jsonEncodeOptions, $ignoreErrors);
        return <<< JSON
```json
{$json}
```
JSON;
    }
}
