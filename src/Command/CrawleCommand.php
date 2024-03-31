<?php

declare(strict_types=1);

namespace App\Command;

use App\Filter\DateRange;
use App\Filter\PageRange;
use App\Source\ProcessConfig;
use App\Source\SourceHandler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:crawle',
    description: 'crawle a news website',
)]
class CrawleCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private readonly SourceHandler $sourceHandler
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->addArgument('source', InputArgument::REQUIRED, 'the website source to crawle');
        $this->addOption('date', null, InputOption::VALUE_OPTIONAL, 'Date interval to crawle', null);
        $this->addOption('page', null, InputOption::VALUE_OPTIONAL, 'PageRange interval to crawle', null);
        $this->addOption('category', null, InputOption::VALUE_OPTIONAL, 'the category to crawle', null);
        $this->addOption('filename', null, InputOption::VALUE_OPTIONAL, 'the filename to save the data', null);
    }

    public function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $source */
        $source = $input->getArgument('source');

        /** @var string|null $page */
        $page = $input->getOption('page');

        /** @var string|null $date */
        $date = $input->getOption('date');

        /** @var string $filename */
        $filename = $input->getOption('filename') ?? $source;

        /** @var string|null $category */
        $category = $input->getOption('category');

        $handled = $this->sourceHandler->process(
            io: $this->io,
            config: new ProcessConfig(
                id: $source,
                filename: $filename,
                page: $page !== null ? PageRange::from($page) : null,
                date: $date !== null ? DateRange::from($date) : null,
                category: $category
            )
        );

        $handled ? $this->io->success('website crawled successfully') : $this->io->error('website not crawled');
        return Command::SUCCESS;
    }
}
