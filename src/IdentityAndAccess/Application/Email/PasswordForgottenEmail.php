<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\Email;

use App\IdentityAndAccess\Domain\Model\ValueObject\Secret\TimedToken;
use App\SharedKernel\Application\Email\Definition;
use App\SharedKernel\Domain\Application;
use App\SharedKernel\Domain\Model\ValueObject\Email;

/**
 * Class PasswordForgottenEmail.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class PasswordForgottenEmail implements Definition
{
    private Application $application;

    public function __construct(
        private Email $recipient,
        private TimedToken $token
    ) {
        $this->application = new Application();
    }

    #[\Override]
    public function recipient(): Email
    {
        return $this->recipient;
    }

    #[\Override]
    public function senderName(): string
    {
        return $this->application->emailName;
    }

    #[\Override]
    public function senderAddress(): string
    {
        return $this->application->emailAddress;
    }

    #[\Override]
    public function subject(): string
    {
        return 'identity_and_access.emails.password_forgotten.subject';
    }

    #[\Override]
    public function subjectVariables(): array
    {
        return [];
    }

    #[\Override]
    public function template(): string
    {
        return 'identity_and_access/password_forgotten';
    }

    #[\Override]
    public function templateVariables(): array
    {
        return [
            'token' => $this->token,
        ];
    }

    #[\Override]
    public function locale(): ?string
    {
        return 'fr';
    }

    #[\Override]
    public function getDomain(): string
    {
        return 'identity_and_access';
    }
}
