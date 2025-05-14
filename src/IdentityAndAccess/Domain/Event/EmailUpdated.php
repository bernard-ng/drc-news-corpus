<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Domain\Event;

use App\IdentityAndAccess\Domain\Model\Identity\UserId;
use App\SharedKernel\Domain\Model\ValueObject\EmailAddress;

/**
 * Class EmailUpdated.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class EmailUpdated
{
    public function __construct(
        public UserId $userId,
        public EmailAddress $previous,
        public EmailAddress $current
    ) {
    }
}
