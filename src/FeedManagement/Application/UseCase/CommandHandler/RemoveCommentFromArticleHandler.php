<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\UseCase\CommandHandler;

use App\FeedManagement\Application\UseCase\Command\RemoveCommentFromArticle;
use App\FeedManagement\Domain\Model\Repository\CommentRepository;
use App\IdentityAndAccess\Domain\Exception\PermissionNotGranted;
use App\SharedKernel\Application\Messaging\CommandHandler;

/**
 * Class RemoveCommentFromArticleHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class RemoveCommentFromArticleHandler implements CommandHandler
{
    public function __construct(
        private CommentRepository $commentRepository,
    ) {
    }

    public function __invoke(RemoveCommentFromArticle $command): void
    {
        $comment = $this->commentRepository->getById($command->commentId);
        if ($command->userId !== $comment->user->id) {
            throw PermissionNotGranted::withReason('feed_management.exceptions.cannot_delete_comment');
        }

        $this->commentRepository->remove($comment);
    }
}
