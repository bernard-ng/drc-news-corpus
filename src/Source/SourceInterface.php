<?php

declare(strict_types=1);

namespace App\Source;

use Symfony\Component\Console\Style\SymfonyStyle;

interface SourceInterface
{
    public function process(SymfonyStyle $io, int $start, int $end, string $filename, ?array $categories = []): void;

    public function supports(string $source): bool;
}
