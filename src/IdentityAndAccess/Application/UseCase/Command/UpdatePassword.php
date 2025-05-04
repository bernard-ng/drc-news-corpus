<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\UseCase\Command;

use App\IdentityAndAccess\Domain\Model\Identity\UserId;

/**
 * Class UpdatePassword.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class UpdatePassword
{
    public function __construct(
        public UserId $userId,
        public string $current,
        public string $password,
    ) {
    }
}
