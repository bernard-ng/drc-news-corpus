<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Application\Mailing;

use App\SharedKernel\Application\Mailing\EmailDefinition;
use App\SharedKernel\Domain\Model\ValueObject\EmailAddress;
use App\SharedKernel\Domain\Model\ValueObject\Tracking\Device;
use App\SharedKernel\Domain\Model\ValueObject\Tracking\GeoLocation;

/**
 * Class LoginProfileChangedEmail.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class LoginProfileChangedEmail implements EmailDefinition
{
    public function __construct(
        private EmailAddress $recipient,
        private Device $device,
        private GeoLocation $location
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
        return 'identity_and_access.emails.subjects.login_profile_changed';
    }

    #[\Override]
    public function subjectVariables(): array
    {
        return [];
    }

    #[\Override]
    public function template(): string
    {
        return 'identity_and_access/login_profile_changed';
    }

    #[\Override]
    public function templateVariables(): array
    {
        return [
            'device' => $this->device,
            'location' => $this->location,
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
