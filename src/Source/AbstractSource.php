<?php

declare(strict_types=1);

namespace App\Source;

use App\Event\CrawleFinishedEvent;
use App\Filter\DateRange;
use App\Filter\PageRange;
use League\Csv\Writer;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class SourceInterface.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
abstract class AbstractSource implements SourceInterface
{
    public const array MONTHS = [
        'janvier' => '01',
        'février' => '02',
        'mars' => '03',
        'avril' => '04',
        'mai' => '05',
        'juin' => '06',
        'juillet' => '07',
        'août' => '08',
        'septembre' => '09',
        'octobre' => '10',
        'novembre' => '11',
        'décembre' => '12',
    ];

    public const array DAYS = [
        'dimanche' => '0',
        'lundi' => '1',
        'mardi' => '2',
        'mercredi' => '3',
        'jeudi' => '4',
        'vendredi' => '5',
        'samedi' => '6',
    ];

    protected const string URL = 'url';

    protected const string ID = 'id';

    protected const string DATE_FORMAT = 'Y-m-d H:i';

    protected string $filename;

    protected Stopwatch $stopwatch;

    protected SymfonyStyle $io;

    protected Writer $writer;

    protected PageRange $page;

    public function __construct(
        #[Autowire('%kernel.project_dir%')] protected string $projectDir,
        #[Autowire('%app_timezone%')] protected string $timezone,
        protected HttpClientInterface $client,
        protected EventDispatcherInterface $dispatcher
    ) {
    }

    public function supports(string $source): bool
    {
        return $source === static::ID;
    }

    /**
     * @throws \Throwable
     */
    protected function initialize(SymfonyStyle $io, string $filename): void
    {
        $this->io = $io;
        $this->stopwatch = new Stopwatch();
        $this->filename = "{$this->projectDir}/data/{$filename}.csv";
        $this->writer = Writer::createFromPath($this->ensureFileExists($this->filename), open_mode: 'a+');
        $this->writer->insertOne(['title', 'link', 'categories', 'body', 'timestamp', 'source']);
        $this->stopwatch->start('crawling');
    }

    /**
     * @throws \Throwable
     */
    protected function createTimeStamp(string $date, string $format, ?string $pattern = null, ?string $replacement = null): string
    {
        /** @var string $date */
        $date = strtr(strtr(strtolower($date), self::DAYS), self::MONTHS);
        if ($pattern !== null && $replacement !== null) {
            /** @var string $date */
            $date = preg_replace(
                pattern: $pattern,
                replacement: $replacement,
                subject: $date
            );
        }

        $datetime = \DateTime::createFromFormat($format, $date);

        return $datetime !== false ?
            $datetime->format('U') :
            (new \DateTime())->format('U');
    }

    /**
     * @throws \Throwable
     */
    protected function crawle(string $url, ?int $page = null): Crawler
    {
        if ($page !== null) {
            if ($this->io->isVerbose()) {
                $this->io->info("> Page {$page}");
            }
        }

        $response = $this->client->request('GET', $url)->getContent();
        return new Crawler($response);
    }

    protected function ensureFileExists(string $filename): string
    {
        if (! file_exists($filename)) {
            touch($filename);
        }

        return $filename;
    }

    protected function dispatchFinished(): void
    {
        $event = $this->stopwatch->stop('crawling');
        $this->dispatcher->dispatch(new CrawleFinishedEvent($event, $this->filename,static::ID));
    }

    /**
     * @throws \Throwable
     */
    protected function getLastPageNumber(?string $url = null): int
    {
        $result = [];

        /** @var string $node */
        $node = $this->crawle($url ?? self::URL)
            ->filter('ul.pagination > li a')
            ->last()
            ->attr('href');

        /** @var string $query */
        $query = parse_url($node, PHP_URL_QUERY);
        parse_str($query, $result);

        return (int) $result['page'];
    }

    protected function skipOnOutOfRange(DateRange $interval, string $timestamp, string $title, string $date): void
    {
        if ($interval->end > (int) $timestamp) {
            $this->dispatchFinished();
            $this->io->success('Done');
            exit;
        }

        if ($this->io->isVerbose()) {
            $this->io->text("> {$title} [Skipped {$date}]");
        }
    }

    /**
     * @throws \Throwable
     */
    protected function writeOnFile(string $title, ?string $link, string $categories, string $body, string $timestamp): void
    {
        $this->writer->insertOne([$title, $link, $categories, $body, $timestamp, static::ID]);

        if ($this->io->isVerbose()) {
            $this->io->text("> {$title} ✅");
        }
    }

    protected function skipOnError(\Throwable|\Exception $e): void
    {
        if ($this->io->isVerbose()) {
            $this->io->text("> {$e->getMessage()} [Failed] ❌");
        }
    }

    protected function onCrawlingCompleted(): void
    {
        try {
            $this->dispatchFinished();
        } finally {
            $this->io->success('Done');
        }
    }
}
