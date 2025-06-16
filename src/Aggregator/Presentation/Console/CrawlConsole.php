<?php

declare(strict_types=1);

namespace App\Aggregator\Presentation\Console;

use App\Aggregator\Domain\Model\ValueObject\Crawling\CrawlingSettings;
use App\Aggregator\Domain\Model\ValueObject\Crawling\PageRange;
use App\Aggregator\Infrastructure\Crawler\Source\SourceCrawler;
use App\SharedKernel\Domain\Model\ValueObject\DateRange;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\PhpSubprocess;

#[AsCommand(
    name: 'app:crawl',
    description: 'crawle a news website',
)]
class CrawlConsole extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private readonly SourceCrawler $sourceCrawler
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addArgument('source', InputArgument::REQUIRED, 'the website source to crawle');
        $this->addOption('date', null, InputOption::VALUE_OPTIONAL, 'Date interval to crawle');
        $this->addOption('page', null, InputOption::VALUE_OPTIONAL, 'PageRange interval to crawle');
        $this->addOption('category', null, InputOption::VALUE_OPTIONAL, 'the category to crawle');
        $this->addOption('parallel', null, InputOption::VALUE_OPTIONAL, 'the number of parallel requests', default: 1);
        $this->addOption('notify', null, InputOption::VALUE_NONE, 'enable notifications');
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

        /** @var string|null $page */
        $page = $input->getOption('page');

        /** @var string|null $date */
        $date = $input->getOption('date');

        /** @var string|null $category */
        $category = $input->getOption('category');

        /** @var string $parallel */
        $parallel = $input->getOption('parallel');
        $parallel = intval($parallel);

        if ($parallel > 1) {
            return $this->parallel($parallel, $source, $category);
        }

        $this->sourceCrawler->fetch(
            settings: new CrawlingSettings(
                id: $source,
                pageRange: $page !== null ? PageRange::from($page) : null,
                dateRange: $date !== null ? DateRange::from($date) : null,
                category: $category,
                notify: $input->getOption('notify') !== null
            )
        );

        $this->io->success('website crawled successfully');
        return Command::SUCCESS;
    }

    private function parallel(int $workers, string $source, ?string $category): int
    {
        $fetcher = $this->sourceCrawler->get($source);
        $range = $fetcher->getPagination($category);
        $workPerWorker = ceil(($range->end - $range->start + 1) / $workers);

        $this->io->title(sprintf('Crawling %d pages with %d workers, %d pages per worker', $range->end - $range->start + 1, $workers, $workPerWorker));

        $processes = [];
        for ($i = 0; $i < $workers; $i++) {
            $start = $range->start + ($i * $workPerWorker);
            $end = min($range->start + (($i + 1) * $workPerWorker) - 1, $range->end);

            $process = new PhpSubprocess(['bin/console', 'app:crawl', $source, sprintf('--page=%d:%d', $start, $end), '-v']);
            $process->start();
            $processes[] = $process;

            if ($start > $range->end) {
                break;
            }
        }

        foreach ($processes as $process) {
            while ($process->isRunning()) {
                // waiting for process to finish
            }

            $this->io->writeln($process->getOutput());
        }

        $this->io->success('Website crawled successfully');
        return Command::SUCCESS;
    }
}
