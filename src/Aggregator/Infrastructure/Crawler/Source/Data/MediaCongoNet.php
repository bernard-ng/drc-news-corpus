<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Crawler\Source\Data;

use App\Aggregator\Domain\ValueObject\DateRange;
use App\Aggregator\Domain\ValueObject\FetchConfig;
use App\Aggregator\Domain\ValueObject\PageRange;
use App\Aggregator\Infrastructure\Crawler\Source\Source;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class MediaCongoNet.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class MediaCongoNet extends Source
{
    public const string URL = 'https://mediacongo.net';

    public const string ID = 'mediacongo.net';

    protected const string DATE_FORMAT = 'd.m.Y';

    /**
     * @throws \Throwable
     */
    #[\Override]
    public function fetch(FetchConfig $config): void
    {
        $this->initialize();
        $page = $config->page ?? PageRange::from(sprintf('1:%d', $this->getLastPage(self::URL . '/articles.html')));

        for ($i = $page->start; $i < $page->end; $i++) {
            try {
                $crawler = $this->crawle(self::URL . "/articles-page-{$i}.html", $i);
                $articles = $crawler->filter('.for_aitems > .article_other_item');
            } catch (\Throwable) {
                continue;
            }

            // loop through the articles and get the title, link, date, categories and body
            $articles->each(fn (Crawler $node) => $this->fetchOne($node->html(), $config->date));
        }

        $this->completed();
    }

    #[\Override]
    public function fetchOne(string $html, ?DateRange $interval = null): void
    {
        $node = new Crawler($html);

        try {
            /** @var string $title */
            $title = $node->filter('img')->attr('alt');

            /** @var string $link */
            $link = $node->filter('a')->first()->attr('href');
            $categories = $node->filter('a.color_link')->text();
            $date = $node->filter('.article_other_about')->text();

            $crawler = $this->crawle(self::URL . "/{$link}");
            $body = $crawler->filter('.article_ttext')->text();
            $timestamp = $this->dateParser->createTimeStamp(
                date: substr($date, 0, 10),
                format: self::DATE_FORMAT,
            );

            if ($interval === null || $interval->inRange((int) $timestamp)) {
                $this->save($title, $link, $categories, $body, $timestamp);
            } else {
                $this->skip($interval, $timestamp, $title, $date);
            }
        } catch (\Throwable $e) {
            $this->logger->critical("> {$e->getMessage()} [Failed] ❌");
            return;
        }
    }

    #[\Override]
    protected function getLastPage(?string $url = null): int
    {
        /** @var string $node */
        $node = $this->crawle($url ?? self::URL)
            ->filter('.nav > a')
            ->last()
            ->attr('href');

        if (preg_match('/(\d+)/', $node, $matches)) {
            $page = $matches[0];
        }

        return isset($page) ? (int) $page : 0;
    }
}
