<?php

declare(strict_types=1);

namespace App\Aggregator\Application\Email;

use App\SharedKernel\Application\Email\EmailDefinition;
use App\SharedKernel\Domain\Model\ValueObject\Email;

/**
 * Class SourceFetched.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class SourceFetchedEmail implements EmailDefinition
{
    public function __construct(
        private Email $recipient,
        private string $event,
        private string $source,
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
        return 'aggregator.emails.source_fetched.subject';
    }

    #[\Override]
    public function subjectVariables(): array
    {
        return [];
    }

    #[\Override]
    public function template(): string
    {
        return 'aggregator/source_fetched';
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
    public function locale(): string
    {
        return 'fr';
    }

    #[\Override]
    public function getDomain(): string
    {
        return 'aggregator';
    }
}
