<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Crawler\Source;

use App\Aggregator\Domain\Service\DateNormalizer;
use App\Aggregator\Domain\Service\SourceFetcher;
use App\Aggregator\Domain\ValueObject\DateRange;
use App\Domain\Event\SourceFetched;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class SourceFetcher.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[AutoconfigureTag('app.data_source')]
abstract class Source implements SourceFetcher
{
    protected const string URL = 'url';

    protected const string ID = 'id';

    protected Stopwatch $stopwatch;

    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        protected string $projectDir,
        #[Autowire('%app_timezone%')]
        protected string $timezone,
        protected HttpClientInterface $client,
        protected EventDispatcherInterface $dispatcher,
        protected LoggerInterface $logger,
        protected DateNormalizer $dateNormalizer
    ) {
    }

    #[\Override]
    public function supports(string $source): bool
    {
        return $source === static::ID;
    }

    /**
     * @throws \Throwable
     */
    protected function initialize(string $filename): void
    {
        if (! file_exists($filename)) {
            touch($filename);
        }

        $this->stopwatch = new Stopwatch();
        $this->stopwatch->start('crawling');
    }

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

    protected function skip(DateRange $interval, string $timestamp, string $title, string $date): void
    {
        if ($interval->end > (int) $timestamp) {
            $event = $this->stopwatch->stop('crawling');
            $this->dispatcher->dispatch(new SourceFetched((string) $event, static::ID));
            $this->logger->info('Done');
            exit;
        }

        $this->logger->info("> {$title} [Skipped {$date}]");
    }

    protected function save(string $title, ?string $link, string $categories, string $body, string $timestamp): void
    {
        try {
            $this->logger->info("> {$title} ✅");
        } catch (\Throwable $e) {
            $this->logger->error("> {$e->getMessage()} [Failed] ❌");
        }
    }

    protected function completed(): void
    {
        try {
            $event = $this->stopwatch->stop('crawling');
            $this->dispatcher->dispatch(new SourceFetched((string) $event, static::ID));
        } finally {
            $this->logger->info('Done');
        }
    }
}
