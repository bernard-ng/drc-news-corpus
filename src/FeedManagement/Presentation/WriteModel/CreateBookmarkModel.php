<?php

declare(strict_types=1);

namespace App\FeedManagement\Presentation\WriteModel;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class CreateBookmarkModel.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class CreateBookmarkModel
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $name;

    #[Assert\Length(max: 512)]
    public ?string $description = null;

    public bool $isPublic = false;
}
