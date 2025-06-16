<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Crawler\Source;

use App\Aggregator\Application\UseCase\Command\CreateArticle;
use App\Aggregator\Domain\Event\SourceCrawled;
use App\Aggregator\Domain\Exception\ArticleOutOfRange;
use App\Aggregator\Domain\Model\ValueObject\Crawling\PageRange;
use App\Aggregator\Domain\Model\ValueObject\Link;
use App\Aggregator\Domain\Service\Crawling\DateParser;
use App\Aggregator\Domain\Service\Crawling\OpenGraph\OpenGraphConsumer;
use App\Aggregator\Domain\Service\Crawling\OpenGraph\OpenGraphObject;
use App\Aggregator\Domain\Service\Crawling\SourceCrawler;
use App\Aggregator\Infrastructure\Crawler\HttpClientFactory;
use App\SharedKernel\Application\Messaging\CommandBus;
use App\SharedKernel\Domain\Model\ValueObject\DateRange;
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

    protected HttpClientInterface $client;

    public function __construct(
        HttpClientFactory $clientFactory,
        protected EventDispatcherInterface $dispatcher,
        protected LoggerInterface $logger,
        protected DateParser $dateParser,
        protected CommandBus $commandBus,
        protected OpenGraphConsumer $openGraphConsumer
    ) {
        $this->stopwatch = new Stopwatch();
        $this->client = $clientFactory->create();
    }

    #[\Override]
    public function supports(string $source): bool
    {
        return $source === $this->getId();
    }

    abstract public function getPagination(?string $category = null): PageRange;

    protected function getId(): string
    {
        return static::ID;
    }

    protected function getUrl(): string
    {
        return static::URL;
    }

    /**
     * @throws \Throwable
     */
    protected function crawle(string $url, ?int $page = null): Crawler
    {
        if ($page !== null) {
            $this->logger->notice('> Page ' . $page);
        }

        $response = $this->client->request('GET', $url)->getContent();
        return new Crawler($response);
    }

    protected function save(
        string $title,
        string $link,
        string $categories,
        string $body,
        string $timestamp,
        ?OpenGraphObject $metadata = null
    ): void {
        try {
            $this->commandBus->handle(
                new CreateArticle(
                    title: $title,
                    link: Link::from($link, $this->getId()),
                    categories: $categories,
                    body: $body,
                    source: $this->getId(),
                    timestamp: (int) $timestamp,
                    metadata: $metadata
                )
            );
            $this->logger->notice(sprintf('> %s ✅', $title));
        } catch (\Throwable $e) {
            $this->logger->error(sprintf('> %s [Failed] ❌', $e->getMessage()));
        }
    }

    protected function initialize(): void
    {
        $this->stopwatch->start(self::WATCH_EVENT_NAME);
        $this->logger->notice('Initialized');
    }

    protected function completed(bool $notify = false): void
    {
        $event = $this->stopwatch->stop(self::WATCH_EVENT_NAME);
        $this->dispatcher->dispatch(new SourceCrawled((string) $event, $this->getId(), $notify));
        $this->logger->notice('Done');
    }

    protected function skip(DateRange $dateRange, string $timestamp, string $title, string $date): void
    {
        if ($dateRange->outRange((int) $timestamp)) {
            throw ArticleOutOfRange::with($timestamp, $dateRange);
        }

        $this->logger->notice(sprintf('> %s [Skipped %s]', $title, $date));
    }

    /**
     * @throws \Throwable
     */
    protected function getLastPage(?string $url = null): int
    {
        $result = [];

        /** @var string $node */
        $node = $this->crawle($url ?? $this->getUrl())
            ->filter('ul.pagination > li a')
            ->last()
            ->attr('href');

        /** @var string $query */
        $query = parse_url($node, PHP_URL_QUERY);
        parse_str($query, $result);

        return (int) $result['page'];
    }
}
