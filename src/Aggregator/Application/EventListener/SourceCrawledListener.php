<?php

declare(strict_types=1);

namespace App\Aggregator\Application\EventListener;

use App\Aggregator\Application\Mailing\SourceCrawledEmail;
use App\Aggregator\Domain\Event\SourceCrawled;
use App\SharedKernel\Application\Mailing\Mailer;
use App\SharedKernel\Domain\EventListener\EventListener;
use App\SharedKernel\Domain\Model\ValueObject\EmailAddress;

/**
 * Class SourceFetchedListener.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class SourceCrawledListener implements EventListener
{
    public function __construct(
        private Mailer $mailer,
        private string $crawlingNotificationEmail
    ) {
    }

    public function __invoke(SourceCrawled $event): void
    {
        if ($event->notify) {
            $email = new SourceCrawledEmail(
                EmailAddress::from($this->crawlingNotificationEmail),
                $event->event,
                $event->source
            );

            $this->mailer->send($email);
        }
    }
}
