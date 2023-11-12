<?php

declare(strict_types=1);

namespace App\Command;

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
    description: 'crawle radio okapi website',
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
        $this->addOption('start', null, InputOption::VALUE_REQUIRED, 'the start page');
        $this->addOption('end', null, InputOption::VALUE_REQUIRED, 'the end page');
        $this->addOption('filename', null, InputOption::VALUE_OPTIONAL, 'the filename to save the data');
        $this->addOption('category', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'the category to crawle');
    }

    public function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $source */
        $source = $input->getArgument('source');

        /** @var string $start */
        $start = $input->getOption('start');

        /** @var string $end */
        $end = $input->getOption('end');

        /** @var string $filename */
        $filename = $input->getOption('filename') ?? $source;

        /** @var array|null $category */
        $category = $input->getOption('category');

        $handled = $this->sourceHandler->process(
            id: $source,
            io: $this->io,
            start: (int) $start,
            end: (int) $end,
            filename: $filename,
            categories: $category
        );

        $handled ? $this->io->success('website crawled successfully') : $this->io->error('website not crawled');
        return Command::SUCCESS;
    }
}
