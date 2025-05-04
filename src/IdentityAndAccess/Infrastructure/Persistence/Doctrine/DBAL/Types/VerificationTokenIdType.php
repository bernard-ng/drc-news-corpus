<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Infrastructure\Persistence\Doctrine\DBAL\Types;

use App\IdentityAndAccess\Domain\Model\Identity\VerificationTokenId;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

/**
 * Class VerificationTokenId.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class VerificationTokenIdType extends AbstractUidType
{
    #[\Override]
    public function getName(): string
    {
        return 'verification_token_id';
    }

    #[\Override]
    protected function getUidClass(): string
    {
        return VerificationTokenId::class;
    }
}
