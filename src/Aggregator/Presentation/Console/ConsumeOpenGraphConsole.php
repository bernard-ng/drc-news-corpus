<?php

declare(strict_types=1);

namespace App\Aggregator\Presentation\Console;

use App\Aggregator\Domain\Event\SourceCrawled;
use App\Aggregator\Domain\Model\Entity\Article;
use App\Aggregator\Domain\Service\Crawling\OpenGraph\OpenGraphConsumer;
use App\Aggregator\Domain\Service\Crawling\OpenGraph\OpenGraphObject;
use App\SharedKernel\Domain\EventDispatcher\EventDispatcher;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;

#[AsCommand(
    name: 'app:open-graph',
    description: 'Update OpenGraph data for articles',
)]
class ConsumeOpenGraphConsole extends Command
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
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addArgument('source', InputArgument::REQUIRED, 'The source to crawl');
        $this->addOption('batch', null, InputOption::VALUE_OPTIONAL, 'Batch size', 50);
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->setProcessTitle('[DRC News] OpenGraph Consumer');
        if ($input->getOption('no-interaction') === false && ! $this->io->confirm('This is a long process, do you want to continue ?', false)) {
            $this->io->warning('Process aborted');
            return Command::SUCCESS;
        }

        $index = 0;
        $batchSize = $input->getOption('batch') ?? 50;
        $source = $input->getArgument('source');

        try {
            $this->entityManager->getConnection()->executeQuery('SET SESSION interactive_timeout = 86400;');
            $this->entityManager->getConnection()->executeQuery('SET SESSION wait_timeout = 86400;');
        } catch (Exception $e) {
            $this->logger->critical('Unable to set session timeout', [
                'exception' => $e,
            ]);
            return Command::FAILURE;
        }

        $query = $this->entityManager
            ->createQuery(<<<'DQL'
                SELECT a 
                FROM App\Aggregator\Domain\Model\Entity\Article a 
                LEFT JOIN App\Aggregator\Domain\Model\Entity\Source s
                WHERE s.name = :source AND a.metadata IS NULL
                ORDER BY a.publishedAt DESC
            DQL)
            ->setParameter('source', $source);

        $this->stopwatch->start(self::WATCH_EVENT_NAME);

        /** @var Article $article */
        foreach ($query->toIterable() as $article) {
            $object = $this->openGraphConsumer->consumeUrl((string) $article->link);

            if ($object instanceof OpenGraphObject) {
                $article->defineOpenGraph($object);
                $this->logger->notice(sprintf('> %s ✅', $article->title));
            } else {
                $this->logger->notice(sprintf('> %s ❌', $article->title));
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
