<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Domain\Event;

use App\IdentityAndAccess\Domain\Model\Entity\Identity\UserId;
use App\SharedKernel\Domain\Model\ValueObject\Email;

/**
 * Class EmailUpdated.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class EmailUpdated
{
    public function __construct(
        public UserId $userId,
        public Email $previous,
        public Email $current
    ) {
    }
}
