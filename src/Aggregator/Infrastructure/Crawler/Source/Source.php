<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Crawler\Source;

use App\Aggregator\Application\UseCase\Command\CreateArticle;
use App\Aggregator\Domain\Event\SourceCrawled;
use App\Aggregator\Domain\Exception\ArticleOutOfRange;
use App\Aggregator\Domain\Model\ValueObject\Crawling\DateRange;
use App\Aggregator\Domain\Model\ValueObject\Crawling\PageRange;
use App\Aggregator\Domain\Service\Crawling\DateParser;
use App\Aggregator\Domain\Service\Crawling\SourceCrawler;
use App\SharedKernel\Application\Messaging\CommandBus;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class SourceFetcher.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[AutoconfigureTag('app.data_source')]
abstract class Source implements SourceCrawler
{
    protected const string URL = 'url';

    protected const string ID = 'id';

    private const string WATCH_EVENT_NAME = 'crawling';

    protected Stopwatch $stopwatch;

    public function __construct(
        protected HttpClientInterface $client,
        protected EventDispatcherInterface $dispatcher,
        protected LoggerInterface $logger,
        protected DateParser $dateParser,
        protected CommandBus $commandBus
    ) {
        $this->stopwatch = new Stopwatch();
    }

    #[\Override]
    public function supports(string $source): bool
    {
        return $source === static::ID;
    }

    abstract public function getPagination(?string $category = null): PageRange;

    /**
     * @throws \Throwable
     */
    protected function crawle(string $url, ?int $page = null): Crawler
    {
        if ($page !== null) {
            $this->logger->info("> Page {$page}");
        }

        $response = $this->client->request('GET', $url)->getContent();
        return new Crawler($response);
    }

    protected function save(string $title, string $link, string $categories, string $body, string $timestamp): void
    {
        try {
            $this->commandBus->handle(
                new CreateArticle(
                    title: $title,
                    link: $link,
                    categories: $categories,
                    body: $body,
                    source: static::ID,
                    timestamp: (int) $timestamp
                )
            );
            $this->logger->info("> {$title} ✅");
        } catch (\Throwable $e) {
            $this->logger->error("> {$e->getMessage()} [Failed] ❌");
        }
    }

    protected function initialize(): void
    {
        $this->stopwatch->start(self::WATCH_EVENT_NAME);
        $this->logger->info('Initialized');
    }

    protected function completed(): void
    {
        $event = $this->stopwatch->stop(self::WATCH_EVENT_NAME);
        $this->dispatcher->dispatch(new SourceCrawled((string) $event, static::ID));
        $this->logger->info('Done');
    }

    protected function skip(DateRange $dateRange, string $timestamp, string $title, string $date): void
    {
        if ($dateRange->outRange((int) $timestamp)) {
            throw ArticleOutOfRange::with($timestamp, $dateRange);
        }

        $this->logger->info("> {$title} [Skipped {$date}]");
    }

    /**
     * @throws \Throwable
     */
    protected function getLastPage(?string $url = null): int
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
}
