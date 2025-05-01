<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Model\ValueObject\Scoring;

/**
 * Class Credibility.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class Credibility implements \JsonSerializable
{
    public function __construct(
        public Bias $bias = Bias::NEUTRAL,
        public Reliability $reliability = Reliability::RELIABLE,
        public Transparency $transparency = Transparency::MEDIUM
    ) {
    }

    public function jsonSerialize(): mixed
    {
        return [
            'bias' => $this->bias->value,
            'reliability' => $this->reliability->value,
            'transparency' => $this->transparency->value,
        ];
    }
}
