<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Crawler\Source\Data;

use App\Aggregator\Domain\ValueObject\DateRange;
use App\Aggregator\Domain\ValueObject\FetchConfig;
use App\Aggregator\Domain\ValueObject\PageRange;
use App\Aggregator\Infrastructure\Crawler\Source\Source;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class PoliticoCdAbstractSource.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class PoliticoCd extends Source
{
    public const string URL = 'https://politico.cd';

    public const string ID = 'politico.cd';

    /**
     * @throws \Throwable
     */
    #[\Override]
    public function fetch(FetchConfig $config): void
    {
        $this->initialize();
        $category = $config->category ?? 'politique';
        $page = $config->page ?? PageRange::from(sprintf('0:%d', $this->getLastPage(self::URL . "/rubrique/{$category}")));

        for ($i = $page->start; $i < $page->end; $i++) {
            try {
                $crawler = $this->crawle(self::URL . "/rubrique/{$category}/page/{$i}", $i);
                $articles = $crawler->filter('article.l-post');
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
            $link = $node->filter('.post-title a')->attr('href');
            $categories = $node->filter('.post-cat a')->text();
            $title = $node->filter('.post-title a')->text();

            /** @var string $date */
            $date = $node->filter('time')->attr('datetime');
            $timestamp = $this->dateParser->createTimeStamp($date, format: 'c');

            if ($interval === null || $interval->inRange((int) $timestamp)) {
                try {
                    $crawler = $this->crawle($link);
                    $body = $crawler->filter('div.post-content.cf.entry-content.content-spacious')->text();
                } catch (\Throwable) {
                    $body = '';
                }

                $this->save($title, $link, $categories, $body, $timestamp);
            } else {
                $this->skip($interval, $timestamp, $title, $date);
            }
        } catch (\Throwable $e) {
            $this->logger->critical("> {$e->getMessage()} [Failed] ❌");
            return;
        }
    }
}
