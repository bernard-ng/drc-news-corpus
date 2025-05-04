<?php

declare(strict_types=1);

namespace App\Aggregator\Presentation\Console;

use App\Aggregator\Application\UseCase\Command\DeleteArticles;
use App\SharedKernel\Application\Messaging\CommandBus;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:delete-articles',
    description: 'remove all articles from the database by source',
)]
class DeleteArticlesConsole extends Command
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
        $this->addArgument('source', InputArgument::REQUIRED, 'the website source to crawle');
        $this->addOption('category', null, InputOption::VALUE_OPTIONAL, 'the category to crawle');
    }

    #[\Override]
    protected function initialize(InputInterface $input, OutputInterface $output): void
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

        if (
            $this->io->confirm('Delete all articles ?', false) &&
            $this->io->confirm('Are you sure ?', false)
        ) {

            $confirmation = $this->io->askQuestion(new Question('Specify the source to confirm : '));
            if ($confirmation === $source) {
                /** @var int $count */
                $count = $this->commandBus->handle(new DeleteArticles($source, $category));
                $this->io->success(sprintf('%d articles from %s removed', $count, $source));
            } else {
                $this->io->warning('Source does not match, aborting !');
            }
        }

        return Command::SUCCESS;
    }
}
