<?php

declare(strict_types=1);

namespace App\Listener;

use App\Event\CrawleFinishedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 * Class CrawleFinishedEventListener.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[AsEventListener(CrawleFinishedEvent::class)]
final readonly class CrawleFinishedEventListener
{
    public function __construct(
        private MailerInterface $mailer
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function __invoke(CrawleFinishedEvent $event): void
    {
        $email = (new Email())
            ->from(new Address('contact@devscast.tech', 'Devscast'))
            ->to(new Address('ngandubernard@gmail.com', 'Bernard Ngandu'))
            ->subject("{$event->source} Crawling done")
            ->text(<<<EOF
            The crawling of {$event->source} is done.
            It took {$event->event}
                        
            EOF)
            ->attachFromPath($event->filename)
            ->attachFromPath(str_ireplace('csv', 'log', $event->filename));
        $this->mailer->send($email);
    }
}
