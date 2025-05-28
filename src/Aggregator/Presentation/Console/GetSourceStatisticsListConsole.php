<?php

declare(strict_types=1);

namespace App\Aggregator\Presentation\Console;

use App\Aggregator\Application\ReadModel\SourceStatistics;
use App\Aggregator\Application\ReadModel\SourceStatisticsList;
use App\Aggregator\Application\UseCase\Query\GetSourceStatisticsList;
use App\SharedKernel\Application\Messaging\QueryBus;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;

#[AsCommand(
    name: 'app:stats',
    description: 'show stats about the articles in the database',
)]
class GetSourceStatisticsListConsole extends Command
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
        /** @var SourceStatisticsList $stats */
        $stats = $this->queryBus->handle(new GetSourceStatisticsList());

        $stopWatch = new Stopwatch(true);
        $stopWatch->start('app:stats');

        $this->io->table(
            ['Source', 'Articles', 'Metadata', 'CrawledAt'],
            array_map(
                fn (SourceStatistics $source): array => [
                    $source->name,
                    number_format($source->articlesCount, decimal_separator: '.', thousands_separator: ','),
                    number_format($source->metadataAvailable, decimal_separator: '.', thousands_separator: ','),
                    $source->crawledAt?->format('Y-m-d H:i:s') ?? 'Never',
                ],
                $stats->items
            )
        );

        $this->io->text((string) $stopWatch->stop('app:stats'));
        return Command::SUCCESS;
    }
}
