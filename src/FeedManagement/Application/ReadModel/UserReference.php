<?php

declare(strict_types=1);

namespace App\FeedManagement\Application\ReadModel;

use App\IdentityAndAccess\Domain\Model\Identity\UserId;
use App\SharedKernel\Domain\DataTransfert\DataMapping;

/**
 * Class UserReference.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class UserReference
{
    public function __construct(
        public UserId $id,
        public string $name,
    ) {
    }

    public static function create(array $item): self
    {
        return new self(
            UserId::fromBinary($item['user_id']),
            DataMapping::string($item, 'user_name')
        );
    }
}
