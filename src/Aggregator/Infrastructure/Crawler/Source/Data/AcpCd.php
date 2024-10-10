<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Crawler\Source\Data;

use App\Aggregator\Domain\ValueObject\DateRange;
use App\Aggregator\Domain\ValueObject\FetchConfig;
use App\Aggregator\Domain\ValueObject\PageRange;
use App\Aggregator\Infrastructure\Crawler\Source\Source;
use Symfony\Component\DomCrawler\Crawler;

final class AcpCd extends source
{
    public const string URL = 'http://acp.cd';

    public const string ID = 'acp.cd';

    /**
     * @throws \Throwable
     */
    #[\Override]
    public function fetch(FetchConfig $config): void
    {
        $this->initialize();
        $page = $config->page ?? PageRange::from(sprintf('0:%d', $this->getLastPage(self::URL . '/acp')));

        for ($i = $page->start; $i < $page->end; $i++) {
            try {
                $crawler = $this->crawle(self::URL . "/acp?page={$i}", $i);
                $articles = $crawler->filter('.td-main-content-wrap .td-main-page-wrap .td-container-wrap');

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
            /** @var string $link */
            $link = $node->filter('.tdb-menu a')->attr('href');
            $category = $node->filter('td-module-container td-category-pos-')->text();
            $title = $node->filter('.entry-title .td-module-title a')->text();

            $crawler = $this->crawle(self::URL . "/{$link}");
            $body = $crawler->filter('body')->text();
            $date = $crawler->filter('.td-post-date')->text();
            $timestamp = $this->dateParser->createTimeStamp(
                date: $date,
                pattern: '/(\d{1}) (\d{2}) (\d{2}) (\d{4}) - (\d{2}:\d{2})/',
                replacement: '$4-$3-$2 $5'
            );

            if ($interval === null || $interval->inRange((int) $timestamp)) {
                $this->save($title, $link, $category, $body, $timestamp);
            } else {
                $this->skip($interval, $timestamp, $title, $date);
            }
        } catch (\Throwable $e) {
            $this->logger->critical("> {$e->getMessage()} [Failed] ❌");
            return;
        }
    }
}
