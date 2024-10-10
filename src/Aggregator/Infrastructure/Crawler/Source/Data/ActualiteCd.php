<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Crawler\Source\Data;

use App\Aggregator\Domain\ValueObject\DateRange;
use App\Aggregator\Domain\ValueObject\FetchConfig;
use App\Aggregator\Domain\ValueObject\PageRange;
use App\Aggregator\Infrastructure\Crawler\Source\Source;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class Politique7sur7Service.
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
    public function fetch(FetchConfig $config): void
    {
        $this->initialize();
        $page = $config->page ?? $this->getPagination();

        for ($i = $page->start; $i < $page->end; $i++) {
            try {
                $crawler = $this->crawle(self::URL . "/actualite?page={$i}", $i);
                $articles = $crawler->filter('#views-bootstrap-taxonomy-term-page-2 > div > div');
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
            $link = $node->filter('#actu-titre a')->attr('href');
            $title = $node->filter('#actu-titre a')->text();
            $categories = $node->filter('#actu-cat a')->text();

            $crawler = $this->crawle(self::URL . "/{$link}");
            $body = $crawler->filter('.views-field.views-field-body')->text();
            $date = $crawler->filter('#p-date')->text();
            $timestamp = $this->dateParser->createTimeStamp(
                date: $date,
                pattern: '/(\d{1}) (\d{1,2}) (\d{2}) (\d{4}) - (\d{2}:\d{2})/',
                replacement: '$4-$3-$2 $5'
            );

            if ($interval === null || $interval->inRange((int) $timestamp)) {
                $this->save($title, $link, $categories, $body, $timestamp);
            } else {
                $this->skip($interval, $timestamp, $title, $date);
            }
        } catch (\Throwable $e) {
            $this->logger->error("> {$e->getMessage()} [Failed] ❌");
            return;
        }
    }

    #[\Override]
    public function getPagination(?string $category = null): PageRange
    {
        return PageRange::from(sprintf('0:%d', $this->getLastPage(self::URL . '/actualite')));
    }
}
