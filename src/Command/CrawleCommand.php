<?php

namespace App\Command;

use League\Csv\Writer;
use League\Csv\UnavailableStream;
use App\Service\PoliticoCdService;
use App\Service\RadioOkapiNetService;
use App\Service\Politique7sur7CdService;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;

#[AsCommand(
    name: 'app:crawle',
    description: 'crawle radio okapi website',
)]
class CrawleCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        public readonly string $projectDir,
        public readonly HttpClientInterface $client,
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->addArgument('website', InputArgument::REQUIRED, 'the website to crawle');
        $this->addArgument('start', InputArgument::REQUIRED, 'the start page');
        $this->addArgument('end', InputArgument::REQUIRED, 'the end page');
        $this->addArgument('filename', InputArgument::OPTIONAL, 'the filename to save the data');
        $this->addArgument('category', InputArgument::OPTIONAL, 'the category to crawle');
    }

    public function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws UnavailableStream
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var int $start */
        $start = $input->getArgument('start');

        /** @var int $end */
        $end = $input->getArgument('end');

        $service = match ($input->getArgument('website')) {
            'radiookapi.net' => new RadioOkapiNetService($this->projectDir, $this->client, $this->io),
            '7sur7.cd' => new Politique7sur7CdService($this->projectDir, $this->client, $this->io),
            'politico.cd' => new PoliticoCdService($this->projectDir, $this->client, $this->io),
        };
        $service->process($start, $end, $input->getArgument('filename'), $input->getArgument('category'));

        $this->io->success('website crawled successfully');
        return Command::SUCCESS;
    }
}
