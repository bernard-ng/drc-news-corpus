<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Class PoliticoService.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
abstract readonly class AbstractService
{
    public const URL = 'https://www.radiookapi.net';

    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        protected string $projectDir,
        protected HttpClientInterface $client,
        protected SymfonyStyle $io,
        protected MailerInterface $mailer
    ) {
    }

    abstract public function process(int $start, int $end, string $filename, ?array $categories = []): void;
}
