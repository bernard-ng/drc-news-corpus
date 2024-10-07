<?php

declare(strict_types=1);

namespace App\Aggregator\Application\Email;

use App\SharedKernel\Application\Email\Definition;
use App\SharedKernel\Domain\Application;
use App\SharedKernel\Domain\Model\ValueObject\Email;

/**
 * Class SourceFetched.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class SourceFetchedEmail implements Definition
{
    private Application $application;

    public function __construct(
        private string $event,
        private string $source,
    ) {
        $this->application = new Application();
    }

    #[\Override]
    public function recipient(): Email
    {
        return Email::from((string) $_ENV['APP_TO_EMAIL']);
    }

    #[\Override]
    public function senderName(): string
    {
        return $this->application->name;
    }

    #[\Override]
    public function senderAddress(): string
    {
        return $this->application->emailAddress;
    }

    #[\Override]
    public function subject(): string
    {
        return 'emails.source_fetched.subject';
    }

    #[\Override]
    public function subjectVariables(): array
    {
        return [];
    }

    #[\Override]
    public function template(): string
    {
        return 'source_fetched';
    }

    #[\Override]
    public function templateVariables(): array
    {
        return [
            'source' => $this->source,
            'event' => $this->event,
        ];
    }

    #[\Override]
    public function locale(): ?string
    {
        return null;
    }

    #[\Override]
    public function getDomain(): string
    {
        return 'aggregator';
    }
}
