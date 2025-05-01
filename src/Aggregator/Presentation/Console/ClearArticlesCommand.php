<?php

declare(strict_types=1);

namespace App\Aggregator\Presentation\Console;

use App\Aggregator\Application\UseCase\Command\ClearArticles;
use App\SharedKernel\Application\Bus\CommandBus;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:clear',
    description: 'remove all articles from the database by source',
)]
class ClearArticlesCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private readonly CommandBus $commandBus
    ) {
        parent::__construct();
    }

    #[\Override]
    public function configure(): void
    {
        $this->addArgument('source', InputArgument::REQUIRED, 'the website source to crawle');
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

        /** @var string|null $category */
        $category = $input->getOption('category');

        /** @var int $count */
        $count = $this->commandBus->handle(new ClearArticles($source, $category));
        $this->io->success(sprintf('%d articles from %s removed', $count, $source));

        return Command::SUCCESS;
    }
}
