<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Infrastructure\Persistence\Doctrine\DBAL\Types;

use App\IdentityAndAccess\Domain\Model\Identity\UserId;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

/**
 * Class UserIdType.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class UserIdType extends AbstractUidType
{
    #[\Override]
    public function getName(): string
    {
        return 'user_id';
    }

    #[\Override]
    protected function getUidClass(): string
    {
        return UserId::class;
    }
}
