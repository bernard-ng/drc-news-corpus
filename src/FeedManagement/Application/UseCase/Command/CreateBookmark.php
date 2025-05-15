<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\UseCase\Command;

use App\IdentityAndAccess\Domain\Model\Identity\UserId;

/**
 * Class CreateBookmark.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class CreateBookmark
{
    public function __construct(
        public UserId $userId,
        public string $name,
        public ?string $description,
    ) {
    }
}
