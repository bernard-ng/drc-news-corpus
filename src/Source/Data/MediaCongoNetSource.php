<?php

declare(strict_types=1);

namespace App\Source\Data;

use App\Filter\DateRange;
use App\Filter\PageRange;
use App\Source\ProcessConfig;
use App\Source\AbstractSource;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Console\Style\SymfonyStyle;

final class MediaCongoNetSource extends AbstractSource
{

    public const string URL = 'https://mediacongo.net';

    public const string ID = 'mediacongo.net';

    protected const string DATE_FORMAT = 'd.m.Y';

    /**
     * @throws \Throwable
     */
    #[\Override]
    public function process(SymfonyStyle $io, ProcessConfig $config): void
    {
        $this->initialize($io, $config->filename);
        $page = $config->page ?? PageRange::from(sprintf('1:%d', $this->getLastPageNumber(self::URL . '/articles.html')));

        for ($i = $page->start; $i < $page->end; $i++) {
            try {
                $crawler = $this->crawle(self::URL . "/articles-page-{$i}.html", $i);
                $articles = $crawler->filter('.for_aitems > .article_other_item');
            } catch (\Throwable $e) {
                continue;
            }

            // loop through the articles and get the title, link, date, categories and body
            $articles->each(fn (Crawler $node) => $this->processNode($node, $config->date));
        }

        $this->onCrawlingCompleted();
    }

    #[\Override]
    public function processNode(Crawler $node, ?DateRange $interval = null): void
    {
        try {
            $title = $node->filter('img')->attr('alt');
            $link = $node->filter('a')->first()->attr('href');
            $categories = $node->filter('a.color_link')->text();
            $date = $node->filter('.article_other_about')->text();

            $crawler = $this->crawle(self::URL . "/{$link}");
            $body = $crawler->filter('.article_ttext')->text();
            $timestamp = $this->createTimeStamp(
                date: substr($date, 0, 10),
                format: self::DATE_FORMAT,
            );

            if ($interval === null || $interval->inRange((int) $timestamp)) {
                $this->writeOnFile($title, $link, $categories, $body, $timestamp);
            } else {
                $this->skipOnOutOfRange($interval, $timestamp, $title, $date);
            }
        } catch (\Throwable $e) {
            $this->skipOnError($e);
            return;
        }
    }

    #[\Override]
    protected function getLastPageNumber(?string $url = null): int
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
