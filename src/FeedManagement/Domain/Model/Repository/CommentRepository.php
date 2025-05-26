<?php

declare(strict_types=1);

namespace App\FeedManagement\Domain\Model\Repository;

use App\FeedManagement\Domain\Model\Entity\Comment;
use App\FeedManagement\Domain\Model\Identity\CommentId;

/**
 * Interface CommentRepository.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface CommentRepository
{
    public function add(Comment $comment): void;

    public function remove(Comment $comment): void;

    public function getById(CommentId $commentId): Comment;
}
