<?php

declare(strict_types=1);

namespace App\Aggregator\Application\Mailing;

use App\SharedKernel\Application\Mailing\EmailDefinition;
use App\SharedKernel\Domain\Model\ValueObject\EmailAddress;

/**
 * Class SourceFetched.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class SourceCrawledEmail implements EmailDefinition
{
    public function __construct(
        private EmailAddress $recipient,
        private string $event,
        private string $source,
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
        return 'aggregator.emails.source_crawled.subject';
    }

    #[\Override]
    public function subjectVariables(): array
    {
        return [];
    }

    #[\Override]
    public function template(): string
    {
        return 'aggregator/source_crawled';
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
