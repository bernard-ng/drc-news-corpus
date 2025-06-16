<?php

declare(strict_types=1);

namespace App\Aggregator\Presentation\Console;

use App\Aggregator\Application\UseCase\Query\GetEarliestPublicationDate;
use App\Aggregator\Application\UseCase\Query\GetLatestPublicationDate;
use App\Aggregator\Domain\Model\ValueObject\Crawling\CrawlingSettings;
use App\Aggregator\Domain\Model\ValueObject\Crawling\UpdateDirection;
use App\Aggregator\Infrastructure\Crawler\Source\SourceCrawler;
use App\SharedKernel\Application\Messaging\QueryBus;
use App\SharedKernel\Domain\Model\ValueObject\DateRange;
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
class UpdateConsole extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private readonly SourceCrawler $sourceCrawler,
        private readonly QueryBus $queryBus
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addArgument('source', InputArgument::REQUIRED, 'the website source to crawle');
        $this->addOption('category', null, InputOption::VALUE_OPTIONAL, 'the category to crawle');
        $this->addOption('direction', null, InputOption::VALUE_OPTIONAL, 'the direction to crawle', 'forward', ['forward', 'backward']);
        $this->addOption('days', null, InputOption::VALUE_OPTIONAL, 'the number of days to crawle');
        $this->addOption('notify', null, InputOption::VALUE_OPTIONAL, 'enable notifications', default: false);
    }

    #[\Override]
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var int|null $days */
        $days = $input->getOption('days');

        /** @var string $source */
        $source = $input->getArgument('source');

        /** @var string|null $category */
        $category = $input->getOption('category');

        /** @var string $direction */
        $direction = $input->getOption('direction');
        $direction = UpdateDirection::from($direction);

        /** @var \DateTimeImmutable $date */
        $date = $this->queryBus->handle(match ($direction) {
            UpdateDirection::FORWARD => new GetLatestPublicationDate($source, $category),
            UpdateDirection::BACKWARD => new GetEarliestPublicationDate($source, $category),
        });

        $dateRange = $direction === UpdateDirection::FORWARD ?
            DateRange::forward($date) :
            DateRange::backward($date, $days);

        $this->io->title(sprintf('[%s] Updating with range %s', $direction->value, $dateRange->format()));
        $this->sourceCrawler->fetch(new CrawlingSettings(
            $source,
            dateRange: $dateRange,
            category: $category,
            notify: $input->getOption('notify') !== null
        ));
        $this->io->success('website crawled successfully');

        return Command::SUCCESS;
    }
}
