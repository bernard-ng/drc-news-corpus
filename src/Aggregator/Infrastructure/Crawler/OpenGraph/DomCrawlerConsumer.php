<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Crawler\OpenGraph;

use App\Aggregator\Domain\Service\Crawling\OpenGraph\Objects\Website;
use App\Aggregator\Domain\Service\Crawling\OpenGraph\OpenGraphConsumer;
use App\Aggregator\Domain\Service\Crawling\OpenGraph\OpenGraphObject;
use App\Aggregator\Domain\Service\Crawling\OpenGraph\OpenGraphProperty;
use App\Aggregator\Infrastructure\Crawler\HttpClientFactory;
use App\Aggregator\Infrastructure\Crawler\UserAgents;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class OpenGraphConsumer.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class DomCrawlerConsumer implements OpenGraphConsumer
{
    private HttpClientInterface $client;

    public function __construct(
        HttpClientFactory $clientFactory,
        private LoggerInterface $logger,
        private bool $useFallbackMode = true,
        private bool $debug = false,
    ) {
        $this->client = $clientFactory->create();
    }

    public function consumeUrl(string $url): ?OpenGraphObject
    {
        try {
            $response = $this->client->request('GET', $url, [
                'headers' => [
                    'User-Agent' => UserAgents::OPEN_GRAPH->value,
                ],
            ])->getContent();

            return $this->consumeHtml($response, $url);
        } catch (\Throwable $e) {
            $this->logger->error(
                'Unable to consume OpenGraph URL',
                [
                    'url' => $url,
                    'exception' => $e,
                ]
            );

            return null;
        }
    }

    public function consumeHtml(string $html, string $fallbackUrl): ?OpenGraphObject
    {
        try {
            $object = $this->consume($html);

            if ($this->useFallbackMode && $object->url === null) {
                $object->url = $fallbackUrl;
            }

            return $object;
        } catch (\Throwable $e) {
            $this->logger->error(
                'Unable to consume OpenGraph HTML',
                [
                    'html' => $html,
                    'exception' => $e,
                ]
            );

            return null;
        }
    }

    private function consume(string $content): OpenGraphObject
    {
        $crawler = new Crawler($content);
        $object = new Website(type: 'website');
        $properties = [];

        foreach (['name', 'property'] as $t) {
            $props = [];

            /** @var \DOMElement $tag */
            foreach ($crawler->filter(sprintf("meta[%s^='og:']", $t)) as $tag) {
                $name = strtolower(trim($tag->getAttribute($t)));
                $value = trim($tag->getAttribute('content'));
                $props[] = new OpenGraphProperty($name, $value);
            }

            $properties = array_merge($properties, $props);
        }

        $object->assignProperties($properties, $this->debug);

        // Fallback for url
        if ($this->useFallbackMode && $object->url === null) {
            $urlElement = $crawler->filter("link[rel='canonical']")->first();
            if ($urlElement->count() > 0) {
                $object->url = trim($urlElement->attr('href') ?? '');
            }
        }

        // Fallback for title
        if ($this->useFallbackMode && $object->title === null) {
            $titleElement = $crawler->filter('title')->first();
            if ($titleElement->count() > 0) {
                $object->title = trim($titleElement->text());
            }
        }

        // Fallback for description
        if ($this->useFallbackMode && $object->description === null) {
            $descriptionElement = $crawler->filter("meta[property='description']")->first();
            if ($descriptionElement->count() > 0) {
                $object->description = trim($descriptionElement->attr('content') ?? '');
            }
        }

        return $object;
    }
}
