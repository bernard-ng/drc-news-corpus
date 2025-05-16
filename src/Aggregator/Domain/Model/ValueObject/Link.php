<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Model\ValueObject;

use App\SharedKernel\Domain\Assert;

/**
 * Class Link.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class Link implements \Stringable, \JsonSerializable
{
    public string $link;

    private function __construct(string $url, ?string $source = null)
    {
        if (! str_starts_with($url, 'http')) {
            Assert::notNull($source, 'You must provide a source if the URL is not absolute.');
            $this->link = sprintf('https://%s/%s', $source, trim($url, '/'));
        } else {
            $this->link = $url;
        }
    }

    public function __toString(): string
    {
        return $this->link;
    }

    public static function from(string $url, ?string $source = null): self
    {
        return new self($url, $source);
    }

    public function jsonSerialize(): string
    {
        return $this->link;
    }
}
