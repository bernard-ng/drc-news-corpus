<?php

declare(strict_types=1);

namespace App\Aggregator\Presentation\Console;

use App\Aggregator\Application\ReadModel\Source\SourceOverview;
use App\Aggregator\Application\ReadModel\Source\SourceOverviewList;
use App\Aggregator\Application\UseCase\Query\GetSourceOverviewList;
use App\SharedKernel\Application\Messaging\QueryBus;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:stats',
    description: 'show stats about the articles in the database',
)]
class GetSourceOverviewListConsole extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private readonly QueryBus $queryBus
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var SourceOverviewList $stats */
        $stats = $this->queryBus->handle(new GetSourceOverviewList());

        $this->io->title('Stats about the articles in the database');
        $this->io->table(
            ['Source', 'Articles', 'Metadata', 'CrawledAt'],
            array_map(
                fn (SourceOverview $stat): array => [
                    $stat->name,
                    number_format($stat->articlesCount, decimal_separator: '.', thousands_separator: ','),
                    number_format($stat->metadataAvailable, decimal_separator: '.', thousands_separator: ','),
                    $stat->crawledAt,
                ],
                $stats->items
            )
        );

        return Command::SUCCESS;
    }
}
