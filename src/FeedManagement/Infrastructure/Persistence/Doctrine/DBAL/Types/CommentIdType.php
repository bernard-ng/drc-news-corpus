<?php

declare(strict_types=1);

namespace App\FeedManagement\Infrastructure\Persistence\Doctrine\DBAL\Types;

use App\FeedManagement\Domain\Model\Identity\CommentId;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

/**
 * Class CommentId.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class CommentIdType extends AbstractUidType
{
    public function getName(): string
    {
        return 'comment_id';
    }

    protected function getUidClass(): string
    {
        return CommentId::class;
    }
}
