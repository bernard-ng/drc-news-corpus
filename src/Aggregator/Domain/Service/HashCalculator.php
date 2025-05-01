<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Service;

/**
 * Class HashCalculator.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class HashCalculator
{
    public function calculate(string $data): string
    {
        return md5($data);
    }
}
