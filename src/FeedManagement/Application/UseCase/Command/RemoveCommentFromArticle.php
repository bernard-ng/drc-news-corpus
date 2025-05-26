<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\UseCase\Command;

use App\FeedManagement\Domain\Model\Identity\CommentId;
use App\IdentityAndAccess\Domain\Model\Identity\UserId;

/**
 * Class RemoveCommentFromArticle.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class RemoveCommentFromArticle
{
    public function __construct(
        public UserId $userId,
        public CommentId $commentId
    ) {
    }
}
