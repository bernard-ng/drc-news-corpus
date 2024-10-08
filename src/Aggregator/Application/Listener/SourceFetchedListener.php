<?php

declare(strict_types=1);

namespace App\Aggregator\Application\Listener;

use App\Aggregator\Application\Email\SourceFetchedEmail;
use App\Aggregator\Domain\Event\SourceFetched;
use App\SharedKernel\Application\Email\Mailer;
use App\SharedKernel\Domain\EventListener\EventListener;

/**
 * Class SourceFetchedListener.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class SourceFetchedListener implements EventListener
{
    public function __construct(
        private Mailer $mailer
    ) {
    }

    public function __invoke(SourceFetched $event): void
    {
        $email = new SourceFetchedEmail($event->event, $event->source);
        $this->mailer->send($email);
    }
}
