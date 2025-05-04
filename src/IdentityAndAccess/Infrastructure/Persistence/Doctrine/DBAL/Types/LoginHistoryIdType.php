<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Infrastructure\Persistence\Doctrine\DBAL\Types;

use App\IdentityAndAccess\Domain\Model\Identity\LoginHistoryId;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

/**
 * Class LoginHistoryId.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class LoginHistoryIdType extends AbstractUidType
{
    #[\Override]
    public function getName(): string
    {
        return 'login_history_id';
    }

    #[\Override]
    protected function getUidClass(): string
    {
        return LoginHistoryId::class;
    }
}
