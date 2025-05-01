<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\Email;

use App\SharedKernel\Application\Mailing\EmailDefinition;
use App\SharedKernel\Domain\Model\ValueObject\Email;

/**
 * Class AccountUnlockedEmail.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class AccountUnlockedEmail implements EmailDefinition
{
    public function __construct(
        private Email $recipient
    ) {
    }

    #[\Override]
    public function recipient(): Email
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
