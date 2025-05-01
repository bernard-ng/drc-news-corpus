<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Service\Scoring;

use App\Aggregator\Domain\Model\ValueObject\Scoring\Bias;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Credibility;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Reliability;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Transparency;

/**
 * Interface CredibilityAnalyser.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface CredibilityAnalyser
{
    public function getBias(string $content): Bias;

    public function getTransparency(string $content): Transparency;

    public function getReliability(string $content): Reliability;

    public function analyse(string $content): Credibility;
}
