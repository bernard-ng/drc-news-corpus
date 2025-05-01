<?php

declare(strict_types=1);

namespace App\Aggregator\Presentation\Console;

use App\Aggregator\Application\ReadModel\Statistics\SourceOverview;
use App\Aggregator\Application\ReadModel\Statistics\SourcesStatisticsOverview;
use App\Aggregator\Application\UseCase\Query\GetSourcesStatisticsOverview;
use App\SharedKernel\Application\Bus\QueryBus;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:stats',
    description: 'show stats about the articles in the database',
)]
class StatsCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private readonly QueryBus $queryBus
    ) {
        parent::__construct();
    }

    #[\Override]
    public function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var SourcesStatisticsOverview $stats */
        $stats = $this->queryBus->handle(new GetSourcesStatisticsOverview());

        $this->io->title(sprintf('Stats about the articles in the database'));
        $this->io->table(
            ['Source', 'Articles', 'CrawledAt'],
            array_map(
                fn (SourceOverview $stat): array => [
                    $stat->source,
                    number_format($stat->articles, decimal_separator: '.', thousands_separator: ','),
                    $stat->crawledAt,
                ],
                $stats->items
            )
        );

        return Command::SUCCESS;
    }
}
