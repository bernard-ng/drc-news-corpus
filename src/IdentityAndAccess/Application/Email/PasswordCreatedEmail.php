<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\Email;

use App\IdentityAndAccess\Domain\Model\ValueObject\Secret\GeneratedCode;
use App\SharedKernel\Application\Email\EmailDefinition;
use App\SharedKernel\Domain\Model\ValueObject\Email;

/**
 * Class PasswordCreatedEmail.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class PasswordCreatedEmail implements EmailDefinition
{
    public function __construct(
        private Email $recipient,
        private GeneratedCode $code
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
        return 'identity_and_access.emails.subjects.password_created';
    }

    #[\Override]
    public function subjectVariables(): array
    {
        return [];
    }

    #[\Override]
    public function template(): string
    {
        return 'identity_and_access/password_created';
    }

    #[\Override]
    public function templateVariables(): array
    {
        return [
            'code' => $this->code,
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
