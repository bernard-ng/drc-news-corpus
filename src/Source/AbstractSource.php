<?php

declare(strict_types=1);

namespace App\Source;

use App\Event\CrawleFinishedEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Stopwatch\Stopwatch;
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
    public const URL = 'url';

    public const ID = 'id';

    public function __construct(
        #[Autowire('%kernel.project_dir%')] protected string $projectDir,
        protected HttpClientInterface $client,
        protected EventDispatcherInterface $dispatcher
    ) {
    }

    public function supports(string $source): bool
    {
        return $source === static::ID;
    }

    protected function createTimeStamp(string $date, string $format = 'd/m/Y - H:m'): string
    {
        $days = [
            'Monday' => 'Lundi',
            'Tuesday' => 'Mardi',
            'Wednesday' => 'Mercredi',
            'Thursday' => 'Jeudi',
            'Friday' => 'Vendredi',
            'Saturday' => 'Samedi',
            'Sunday' => 'Dimanche',
        ];

        $months = [
            'January' => 'Janvier',
            'February' => 'Février',
            'March' => 'Mars',
            'April' => 'Avril',
            'May' => 'Mai',
            'June' => 'Juin',
            'July' => 'Juillet',
            'August' => 'Août',
            'September' => 'Septembre',
            'October' => 'Octobre',
            'November' => 'Novembre',
            'December' => 'Décembre',
        ];

        $date = str_ireplace(array_keys($days), array_values($days), $date);
        $date = str_ireplace(array_keys($months), array_values($months), $date);
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
