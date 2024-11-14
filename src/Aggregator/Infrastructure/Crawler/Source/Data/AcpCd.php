<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Crawler\Source\Data;

use App\Aggregator\Domain\ValueObject\DateRange;
use App\Aggregator\Domain\ValueObject\FetchConfig;
use App\Aggregator\Domain\ValueObject\PageRange;
use App\Aggregator\Infrastructure\Crawler\Source\Source;
use Symfony\Component\DomCrawler\Crawler;

final class AcpCd extends Source
{
    public const string URL = 'https://acp.cd';

    public const string ID = 'acp.cd';

    /**
     * @throws \Throwable
     */
    #[\Override]
    public function fetch(FetchConfig $config): void
    {
        $this->initialize();

        $page = $config->page ?? PageRange::from('1:10'); // Exemples de pages, ajustables

        for ($i = $page->start; $i <= $page->end; $i++) {
            try {

                $ajaxUrl = self::URL . "/ajax/fil-actu?page={$i}";
                $crawler = $this->crawle($ajaxUrl, $i);

                $articles = $crawler->filter('.td-main-content-wrap .td-module-container');
            } catch (\Throwable) {
                continue;
            }

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
            $link = $node->filter('.td-module-thumb a')->attr('href');

            // Vérifiez si le lien commence par 'http' ou 'https' et nettoyez-le
            if (strpos($link, 'http') === 0) {
                // Si le lien est absolu, ne pas le préfixer avec self::URL
                $absoluteLink = $link;
            } else {
                // Si le lien est relatif, le préfixer avec self::URL
                $absoluteLink = self::URL . '/' . ltrim($link, '/');
            }

            $category = $node->filter('.td-module-meta-info .td-post-category')->text();
            $title = $node->filter('.td-module-title a')->text();

            // Utilisez l'URL correctement construite
            $crawler = $this->crawle($absoluteLink);
            $body = $crawler->filter('.td-post-content')->text();
            $date = $crawler->filter('.td-post-date')->text();
            $timestamp = $this->dateParser->createTimeStamp(
                date: $date,
                pattern: '/(\d{2})-(\d{2})-(\d{4}) (\d{2}:\d{2})/',
                replacement: '$3-$2-$1 $4'
            );

            if ($interval === null || $interval->inRange((int) $timestamp)) {
                $this->save($title, $absoluteLink, $category, $body, $timestamp);
            } else {
                $this->skip($interval, $timestamp, $title, $date);
            }
        } catch (\Throwable $e) {
            $this->logger->critical("> {$e->getMessage()} [Failed] ❌");
            return;
        }
    }

    #[\Override]
    public function getPagination(?string $category = null): PageRange
    {
        return PageRange::from('1:10'); // Nombre de pages à adapter dynamiquement
    }
}
