<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Service\Scoring;

use App\Aggregator\Domain\Model\ValueObject\Scoring\Sentiment;

/**
 * Interface SentimentAnalyser.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface SentimentAnalyser
{
    public function analyse(string $content): Sentiment;
}
