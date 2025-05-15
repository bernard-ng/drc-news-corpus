<?php

declare(strict_types=1);

namespace App\FeedManagement\Infrastructure\Persistence\Doctrine\DBAL\Types;

use App\FeedManagement\Domain\Model\Identity\BookmarkId;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

/**
 * Class BookmarkIdType.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class BookmarkIdType extends AbstractUidType
{
    public function getName(): string
    {
        return 'bookmark_id';
    }

    protected function getUidClass(): string
    {
        return BookmarkId::class;
    }
}
