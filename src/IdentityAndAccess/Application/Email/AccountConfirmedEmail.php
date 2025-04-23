<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\Email;

use App\SharedKernel\Application\Email\EmailDefinition;
use App\SharedKernel\Domain\Model\ValueObject\Email;

/**
 * Class UserConfirmedEmail.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class AccountConfirmedEmail implements EmailDefinition
{
    public function __construct(
        private Email $recipient,
        private bool $isSocialLogin,
        private ?string $socialLoginService
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
        return 'identity_and_access.emails.subjects.account_confirmed';
    }

    #[\Override]
    public function subjectVariables(): array
    {
        return [];
    }

    #[\Override]
    public function template(): string
    {
        return 'identity_and_access/account_confirmed';
    }

    #[\Override]
    public function templateVariables(): array
    {
        return [
            'is_social_login' => $this->isSocialLogin,
            'social_login_service' => $this->socialLoginService,
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
