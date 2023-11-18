<?php

declare(strict_types=1);

namespace App\Listener;

use App\Event\CrawleFinishedEvent;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
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
        #[Autowire('%app_from_email%')] private string $fromEmail,
        #[Autowire('%app_to_email%')] private string $toEmail,
        private MailerInterface $mailer
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function __invoke(CrawleFinishedEvent $event): void
    {
        $info = new \SplFileInfo($event->filename);

        $email = (new Email())
            ->from(new Address($this->fromEmail))
            ->to(new Address($this->toEmail))
            ->subject("{$event->source} Crawling done")
            ->text(<<<EOF
            The crawling of {$event->source} is done.
            It took {$event->event}
                        
            The file is available at {$info->getRealPath()}
            - file size: {$info->getSize()} bytes 
            - file extension: {$info->getExtension()}
            - file type: {$info->getType()}
            EOF);
        $this->mailer->send($email);
    }
}
