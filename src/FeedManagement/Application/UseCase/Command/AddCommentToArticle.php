<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\UseCase\Command;

use App\Aggregator\Domain\Model\Identity\ArticleId;
use App\IdentityAndAccess\Domain\Model\Identity\UserId;

/**
 * Class AddCommentToArticle.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class AddCommentToArticle
{
    public function __construct(
        public UserId $userId,
        public ArticleId $articleId,
        public string $content,
    ) {
    }
}
