<?php

declare(strict_types=1);

namespace App\Aggregator\Presentation\Console;

use App\Aggregator\Application\UseCase\Query\GetLastCrawlDateQuery;
use App\Aggregator\Domain\ValueObject\DateRange;
use App\Aggregator\Domain\ValueObject\FetchConfig;
use App\Aggregator\Infrastructure\Crawler\Source\SourceFetcher;
use App\SharedKernel\Application\Bus\QueryBus;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update',
    description: 'crawl a news website based on last update',
)]
class UpdateCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private readonly SourceFetcher $sourceFetcher,
        private readonly QueryBus $queryBus
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

        /** @var string $lastUpdate */
        $lastUpdate = $this->queryBus->handle(new GetLastCrawlDateQuery($source, $category));
        $today = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $range = DateRange::from(sprintf('%s--%s', $lastUpdate, $today), 'Y-m-d H:i:s', '--');

        $this->io->title(sprintf('Updating database with article from %s to %s', $lastUpdate, $today));
        $this->sourceFetcher->fetch(new FetchConfig($source, date: $range, category: $category));
        $this->io->success('website crawled successfully');

        return Command::SUCCESS;
    }
}
