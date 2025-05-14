<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\Mailing;

use App\SharedKernel\Application\Mailing\EmailDefinition;
use App\SharedKernel\Domain\Model\ValueObject\EmailAddress;

/**
 * Class AccountUnlockedEmail.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class AccountUnlockedEmail implements EmailDefinition
{
    public function __construct(
        private EmailAddress $recipient
    ) {
    }

    #[\Override]
    public function recipient(): EmailAddress
    {
        return $this->recipient;
    }

    #[\Override]
    public function subject(): string
    {
        return 'identity_and_access.emails.subjects.account_unlocked';
    }

    #[\Override]
    public function subjectVariables(): array
    {
        return [];
    }

    #[\Override]
    public function template(): string
    {
        return 'identity_and_access/account_unlocked';
    }

    #[\Override]
    public function templateVariables(): array
    {
        return [];
    }

    #[\Override]
    public function locale(): string
    {
        return 'fr';
    }

    #[\Override]
    public function getDomain(): string
    {
        return 'identity_and_access';
    }
}
