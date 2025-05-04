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
 * Class ActualiteCd.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class ActualiteCd extends Source
{
    public const string URL = 'https://actualite.cd';

    public const string ID = 'actualite.cd';

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
                $crawler = $this->crawle(self::URL . ('/actualite?page=' . $i), $i);
                $articles = $crawler->filter('#views-bootstrap-taxonomy-term-page-2 > div > div');
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
            /** @var string $link */
            $link = $node->filter('#actu-titre a')->attr('href');
            $title = $node->filter('#actu-titre a')->text();
            $categories = $node->filter('#actu-cat a')->text();

            $crawler = $this->crawle(self::URL . ('/' . $link));
            $metadata = $this->openGraphConsumer->consumeHtml($crawler->html(), self::URL . ('/' . $link));

            $body = $crawler->filter('.views-field.views-field-body')->text();
            $date = $crawler->filter('#p-date')->text();
            $timestamp = $this->dateParser->createTimeStamp(
                date: $date,
                pattern: '/(\d{1}) (\d{1,2}) (\d{2}) (\d{4}) - (\d{2}:\d{2})/',
                replacement: '$4-$3-$2 $5'
            );

            if (! $dateRange instanceof DateRange || $dateRange->inRange((int) $timestamp)) {
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
        return PageRange::from(sprintf('0:%d', $this->getLastPage(self::URL . '/actualite')));
    }
}
