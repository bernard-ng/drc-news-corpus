<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\Mailing;

use App\IdentityAndAccess\Domain\Model\ValueObject\Secret\GeneratedToken;
use App\SharedKernel\Application\Mailing\EmailDefinition;
use App\SharedKernel\Domain\Model\ValueObject\EmailAddress;

/**
 * Class UserRegisteredEmail.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class ConfirmationRequestedEmail implements EmailDefinition
{
    public function __construct(
        private EmailAddress $recipient,
        private string $name,
        private GeneratedToken $token
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
        return 'identity_and_access.emails.subjects.user_registered';
    }

    #[\Override]
    public function subjectVariables(): array
    {
        return [];
    }

    #[\Override]
    public function template(): string
    {
        return 'identity_and_access/user_registered';
    }

    #[\Override]
    public function templateVariables(): array
    {
        return [
            'name' => $this->name,
            'token' => $this->token,
        ];
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
