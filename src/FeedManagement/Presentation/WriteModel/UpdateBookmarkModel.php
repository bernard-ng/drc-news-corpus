<?php

declare(strict_types=1);

namespace App\FeedManagement\Presentation\WriteModel;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UpdateBookmarkModel.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class UpdateBookmarkModel
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $name;

    #[Assert\Length(max: 2048)]
    public ?string $description = null;

    public bool $isPublic = false;
}
