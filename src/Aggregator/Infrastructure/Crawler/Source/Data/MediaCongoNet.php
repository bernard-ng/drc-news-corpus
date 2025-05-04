<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Crawler\Source\Data;

use App\Aggregator\Domain\Exception\ArticleOutOfRange;
use App\Aggregator\Domain\Model\ValueObject\Crawling\CrawlingSettings;
use App\Aggregator\Domain\Model\ValueObject\Crawling\DateRange;
use App\Aggregator\Domain\Model\ValueObject\Crawling\PageRange;
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

    protected const string DATE_FORMAT = 'd.m.Y H:i';

    /**
     * @throws \Throwable
     */
    #[\Override]
    public function fetch(CrawlingSettings $settings): void
    {
        $this->initialize();
        $page = $settings->pageRange ?? $this->getPagination();

        for ($i = $page->start; $i < $page->end; $i++) {
            try {
                $crawler = $this->crawle(self::URL . sprintf('/articles-page-%d.html', $i), $i);
                $articles = $crawler->filter('.for_aitems > .article_other_item');
            } catch (\Throwable) {
                continue;
            }

            try {
                $articles->each(fn (Crawler $node) => $this->fetchOne($node->html(), $settings->dateRange));
            } catch (ArticleOutOfRange) {
                $this->logger->notice('No more articles to fetch in this range.');
                break;
            }
        }

        $this->completed();
    }

    #[\Override]
    public function fetchOne(string $html, ?DateRange $dateRange = null): void
    {
        $node = new Crawler($html);

        try {
            /** @var string $title */
            $title = $node->filter('img')->attr('alt');

            /** @var string $link */
            $link = $node->filter('a')->first()->attr('href');
            $categories = $node->filter('a.color_link')->text();
            $date = $node->filter('.article_other_about')->text();
            $timestamp = $this->dateParser->createTimeStamp(
                date: sprintf('%s %s', substr($date, 0, 10), '00:00'),
                format: self::DATE_FORMAT,
            );

            if (! $dateRange instanceof DateRange || $dateRange->inRange((int) $timestamp)) {
                $crawler = $this->crawle(self::URL . ('/' . $link));
                $metadata = $this->openGraphConsumer->consumeHtml($crawler->html(), self::URL . ('/' . $link));
                $body = $crawler->filter('.article_ttext')->text();

                $this->save($title, $link, $categories, $body, $timestamp, $metadata);
            } else {
                $this->skip($dateRange, $timestamp, $title, $date);
            }
        } catch (ArticleOutOfRange $e) {
            throw $e;
        } catch (\Throwable $e) {
            $this->logger->error(sprintf('> %s [Failed] ❌', $e->getMessage()));
            return;
        }
    }

    #[\Override]
    public function getPagination(?string $category = null): PageRange
    {
        return PageRange::from(sprintf('1:%d', $this->getLastPage(self::URL . '/articles.html')));
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
