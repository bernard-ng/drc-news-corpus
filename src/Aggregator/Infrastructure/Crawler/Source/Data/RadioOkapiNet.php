<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Crawler\Source\Data;

use App\Aggregator\Domain\Exception\ArticleOutOfRange;
use App\Aggregator\Domain\Model\ValueObject\Crawling\CrawlingSettings;
use App\Aggregator\Domain\Model\ValueObject\Crawling\PageRange;
use App\Aggregator\Infrastructure\Crawler\Source\Source;
use App\SharedKernel\Domain\Model\ValueObject\DateRange;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class RadioOkapiNet.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class RadioOkapiNet extends Source
{
    public const string URL = 'https://www.radiookapi.net';

    public const string ID = 'radiookapi.net';

    /**
     * @throws \Throwable
     */
    #[\Override]
    public function fetch(CrawlingSettings $settings): void
    {
        $this->initialize();
        $page = $settings->pageRange ?? $this->getPagination();

        for ($i = $page->start; $i <= $page->end; $i++) {
            try {
                $crawler = $this->crawle(self::URL . ('/actualite?page=' . $i), $i);
                $articles = $crawler->filter('.view-content')->children('.views-row.content-row');
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

        $this->completed($settings->notify);
    }

    #[\Override]
    public function fetchOne(string $html, ?DateRange $dateRange = null): void
    {
        $node = new Crawler($html);

        try {
            /** @var string $link */
            $link = $node->filter('.views-field-title a')->attr('href');
            $date = $node->filter('.views-field-created')->text();
            $timestamp = $this->dateParser->createTimeStamp(
                date: $date,
                pattern: '/(\d{2})\/(\d{2})\/(\d{4}) - (\d{2}:\d{2})/',
                replacement: '$3-$2-$1 $4'
            );
            $categories = $node->filter('.views-field-field-cat-gorie a')->each(fn (Crawler $node): string => $node->text());
            $title = $node->filter('.views-field-title a')->text();

            if (! $dateRange instanceof DateRange || $dateRange->inRange((int) $timestamp)) {
                $crawler = $this->crawle(self::URL . ('/' . $link));
                $metadata = $this->openGraphConsumer->consumeHtml($crawler->html(), self::URL . ('/' . $link));
                $body = $crawler->filter('.field-name-body')->text();

                $this->save($title, $link, strtolower(implode(',', $categories)), $body, $timestamp, $metadata);
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
