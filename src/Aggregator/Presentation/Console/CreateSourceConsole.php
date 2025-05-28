<?php

declare(strict_types=1);

namespace App\Aggregator\Presentation\Console;

use App\Aggregator\Application\UseCase\Command\CreateSource;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Bias;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Credibility;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Reliability;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Transparency;
use App\SharedKernel\Application\Messaging\CommandBus;
use App\SharedKernel\Presentation\Console\AskArgumentFeature;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-source',
    description: 'add a new data source'
)]
class CreateSourceConsole extends Command
{
    use AskArgumentFeature;

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
        $this->addArgument('displayName', InputArgument::OPTIONAL, 'the display name of the source');
        $this->addArgument('description', InputArgument::OPTIONAL, 'the description of the source');
        $this->addOption('bias', 'b', InputArgument::OPTIONAL, 'bias of the source', Bias::NEUTRAL->value);
        $this->addOption('reliability', 'r', InputArgument::OPTIONAL, 'reliability of the source', Reliability::AVERAGE->value);
        $this->addOption('transparency', 't', InputArgument::OPTIONAL, 'transparency of the source', Transparency::MEDIUM->value);
    }

    #[\Override]
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->io->title('Create a new data source');

        $this->askArgument($input, 'source');
        $this->askArgument($input, 'displayName');
        $this->askOption($input, 'bias');
        $this->askOption($input, 'reliability');
        $this->askOption($input, 'transparency');
    }

    #[\Override]
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (! $this->io->confirm('Do you want to continue?', false)) {
            $this->io->warning('Process aborted');
            return Command::FAILURE;
        }

        /** @var string $source */
        $source = $input->getArgument('source');

        /** @var string|null $displayName */
        $displayName = $input->getArgument('displayName');

        /** @var string|null $description */
        $description = $input->getArgument('description');

        $credibility = new Credibility(
            bias: Bias::from($input->getOption('bias')),
            reliability: Reliability::from($input->getOption('reliability')),
            transparency: Transparency::from($input->getOption('transparency')),
        );

        $this->commandBus->handle(new CreateSource($source, $credibility, $displayName, $description));

        $this->io->success('Source add successfully');
        return Command::SUCCESS;
    }
}
