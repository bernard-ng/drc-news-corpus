<?php

declare(strict_types=1);

namespace App\Aggregator\Presentation\Console;

use App\Aggregator\Domain\ValueObject\DateRange;
use App\Aggregator\Domain\ValueObject\FetchConfig;
use App\Aggregator\Domain\ValueObject\PageRange;
use App\Aggregator\Infrastructure\Crawler\Source\SourceFetcher;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:crawl',
    description: 'crawle a news website',
)]
class CrawlCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private readonly SourceFetcher $sourceFetcher
    ) {
        parent::__construct();
    }

    #[\Override]
    public function configure(): void
    {
        $this->addArgument('source', InputArgument::REQUIRED, 'the website source to crawle');
        $this->addOption('date', null, InputOption::VALUE_OPTIONAL, 'Date interval to crawle');
        $this->addOption('page', null, InputOption::VALUE_OPTIONAL, 'PageRange interval to crawle');
        $this->addOption('category', null, InputOption::VALUE_OPTIONAL, 'the category to crawle');
    }

    #[\Override]
    public function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $source */
        $source = $input->getArgument('source');

        /** @var string|null $page */
        $page = $input->getOption('page');

        /** @var string|null $date */
        $date = $input->getOption('date');

        /** @var string|null $category */
        $category = $input->getOption('category');

        $this->sourceFetcher->fetch(
            config: new FetchConfig(
                id: $source,
                page: $page !== null ? PageRange::from($page) : null,
                date: $date !== null ? DateRange::from($date) : null,
                category: $category
            )
        );

        $this->io->success('website crawled successfully');
        return Command::SUCCESS;
    }
}
