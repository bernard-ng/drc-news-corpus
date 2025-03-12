<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Framework\Symfony\Mailer;

use App\SharedKernel\Application\Email\Definition;
use App\SharedKernel\Application\Email\Mailer;
use App\SharedKernel\Domain\Application;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class SymfonyMailer.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class SymfonyMailer implements Mailer
{
    public function __construct(
        private MailerInterface $mailer,
        private TranslatorInterface $translator
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[\Override]
    public function send(Definition $email): void
    {
        $sender = new Address($email->senderAddress(), $email->senderName());

        $htmlTemplate = sprintf('emails/%s.html.twig', $email->template());
        $txtTemplate = sprintf('emails/%s.txt.twig', $email->template());

        $message = (new TemplatedEmail())
            ->from($sender)
            ->to($email->recipient()->value)
            ->subject(
                $this->translator->trans(
                    $email->subject(),
                    $email->subjectVariables(),
                    $email->getDomain(),
                    $email->locale()
                )
            )
            ->htmlTemplate($htmlTemplate)
            ->textTemplate($txtTemplate)
            ->context(array_merge(
                $email->templateVariables(),
                [
                    'application' => new Application(),
                    'locale' => $email->locale(),
                    'domain' => $email->getDomain(),
                ]
            ))
        ;

        $this->mailer->send($message);
    }
}
