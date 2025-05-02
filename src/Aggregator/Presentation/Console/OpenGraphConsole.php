<?php

declare(strict_types=1);

namespace App\Aggregator\Presentation\Console;

use App\Aggregator\Domain\Event\SourceCrawled;
use App\Aggregator\Domain\Model\Entity\Article;
use App\Aggregator\Domain\Service\Crawling\OpenGraph\OpenGraphConsumer;
use App\SharedKernel\Domain\EventDispatcher\EventDispatcher;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;

#[AsCommand(
    name: 'app:open-graph',
    description: 'Update OpenGraph data for articles',
)]
class OpenGraphConsole extends Command
{
    private const string WATCH_EVENT_NAME = 'open-graph-consume';

    private SymfonyStyle $io;

    public function __construct(
        private readonly OpenGraphConsumer $openGraphConsumer,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
        private readonly EventDispatcher $eventDispatcher,
        private readonly Stopwatch $stopwatch = new Stopwatch(false)
    ) {
        parent::__construct();
    }

    #[\Override]
    public function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    #[\Override]
    public function configure(): void
    {
        $this->addOption('batch', null, InputOption::VALUE_OPTIONAL, 'Batch size', 50);
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->setProcessTitle('[DRC News] OpenGraph Consumer');
        if ($input->getOption('no-interaction') === false) {
            if (! $this->io->confirm('This is a long process, do you want to continue ?', false)) {
                $this->io->warning('Process aborted');
                return Command::SUCCESS;
            }
        }

        $index = 0;
        $batchSize = $input->getOption('batch') ?? 50;
        $query = $this->entityManager->createQuery(
            sprintf('SELECT a FROM %s a WHERE a.metadata IS NULL ORDER BY a.publishedAt DESC', Article::class)
        );

        $this->stopwatch->start(self::WATCH_EVENT_NAME);

        /** @var Article $article */
        foreach ($query->toIterable() as $article) {
            $object = $this->openGraphConsumer->consumeUrl((string) $article->link);

            if ($object !== null) {
                $article->defineOpenGraph($object);
                $this->logger->notice("> {$article->title} ✅");
            } else {
                $this->logger->notice("> {$article->title} ❌");
            }

            ++$index;
            if ($index % $batchSize === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear();
            }
        }
        $this->entityManager->flush();

        $event = $this->stopwatch->stop(self::WATCH_EVENT_NAME);
        $this->eventDispatcher->dispatch([new SourceCrawled((string) $event, 'open-graph')]);
        $this->logger->notice('OpenGraph data fetched successfully');
        return Command::SUCCESS;
    }
}
