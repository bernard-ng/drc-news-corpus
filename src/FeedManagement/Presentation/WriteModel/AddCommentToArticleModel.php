<?php

declare(strict_types=1);

namespace App\FeedManagement\Presentation\WriteModel;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class AddCommentToArticleModel.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class AddCommentToArticleModel
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 512)]
    public string $content;
}
