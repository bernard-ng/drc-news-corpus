<?php

declare(strict_types=1);

namespace App\Source;

use App\Filter\DateRange;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Interface SourceInterface.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface SourceInterface
{
    public function process(SymfonyStyle $io, ProcessConfig $config): void;

    public function processNode(Crawler $node, ?DateRange $interval = null): void;

    public function supports(string $source): bool;
}
