<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL\Types;

use App\Aggregator\Domain\Model\Identity\SourceId;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

/**
 * Class SourceId.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class SourceIdType extends AbstractUidType
{
    public function getName(): string
    {
        return 'source_id';
    }

    protected function getUidClass(): string
    {
        return SourceId::class;
    }
}
