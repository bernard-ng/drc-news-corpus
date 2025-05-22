<?php

declare(strict_types=1);

namespace App\FeedManagement\Infrastructure\Persistence\Doctrine\DBAL\Types;

use App\FeedManagement\Domain\Model\Identity\FollowedSourceId;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

/**
 * Class FollowedSourceIdType.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class FollowedSourceIdType extends AbstractUidType
{
    public function getName(): string
    {
        return 'followed_source_id';
    }

    protected function getUidClass(): string
    {
        return FollowedSourceId::class;
    }
}
