<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Model\Repository;

use App\Aggregator\Domain\Model\Entity\Source;
use App\Aggregator\Domain\Model\Identity\SourceId;

/**
 * Interface SourceRepository.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface SourceRepository
{
    public function add(Source $source): void;

    public function remove(Source $source): void;

    public function getByName(string $name): Source;

    public function getById(SourceId $sourceId): Source;
}
