<?php

declare(strict_types=1);

namespace App\Aggregator\Presentation\Console;

use App\Aggregator\Application\UseCase\Command\ExportArticles;
use App\SharedKernel\Application\Messaging\CommandBus;
use App\SharedKernel\Domain\Model\ValueObject\DateRange;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:export-articles',
    description: 'export crawled news website',
)]
final class ExportArticlesConsole extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private readonly CommandBus $commandBus
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addArgument('source', InputArgument::OPTIONAL, 'the website source to crawle');
        $this->addOption('date', null, InputOption::VALUE_OPTIONAL, 'Date interval to crawle', null);
    }

    #[\Override]
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string|null $source */
        $source = $input->getArgument('source');

        /** @var string|null $date */
        $date = $input->getOption('date');

        $confirmation = $this->io->confirm('This can take a while, would like to continue ?', false);
        if ($confirmation) {
            $this->commandBus->handle(new ExportArticles(
                source: $source,
                date: $date !== null ? DateRange::from($date) : null
            ));
        }

        $this->io->success('articles exported successfully');
        return Command::SUCCESS;
    }
}
