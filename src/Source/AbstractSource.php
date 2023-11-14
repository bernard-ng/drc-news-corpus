<?php

declare(strict_types=1);

namespace App\Source;

use App\Event\CrawleFinishedEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchPeriod;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class SourceInterface.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
abstract readonly class AbstractSource implements SourceInterface
{
    public const URL = 'https://www.radiookapi.net';

    public const ID = 'radiookapi.net';

    public function __construct(
        #[Autowire('%kernel.project_dir%')] protected string $projectDir,
        protected HttpClientInterface $client,
        protected EventDispatcherInterface $dispatcher
    ) {
    }

    protected function createTimeStamp(string $date, string $format = 'd/m/Y - H:m'): string
    {
        $date = \DateTime::createFromFormat($format, $date);
        return $date !== false ? $date->format('U') : (new \DateTime())->format('U');
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function crawle(string $url): Crawler
    {
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

    protected function dispatchCrawleFinishedEvent(Stopwatch $stopwatch, string $filename): void
    {
        $event = $stopwatch->stop('crawling');
        $this->dispatcher->dispatch(new CrawleFinishedEvent($event, $filename,static::ID));
    }
}
