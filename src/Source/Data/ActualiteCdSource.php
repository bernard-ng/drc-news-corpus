<?php

declare(strict_types=1);

namespace App\Source\Data;

use App\Filter\DateRange;
use App\Filter\PageRange;
use App\Source\AbstractSource;
use App\Source\ProcessConfig;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class Politique7sur7Service.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[AsTaggedItem('app.data_source')]
final class ActualiteCdSource extends AbstractSource
{
    public const string URL = 'https://actualite.cd';

    public const string ID = 'actualite.cd';

    /**
     * @throws \Throwable
     */
    #[\Override]
    public function process(SymfonyStyle $io, ProcessConfig $config): void
    {
        $this->initialize($io, $config->filename);
        $page = $config->page ?? PageRange::from(sprintf('0:%d', $this->getLastPageNumber(self::URL . '/actualite')));

        for ($i = $page->start; $i < $page->end; $i++) {
            try {
                $crawler = $this->crawle(self::URL . "/actualite?page={$i}", $i);
                $articles = $crawler->filter('#views-bootstrap-taxonomy-term-page-2 > div > div');
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
            $title = $node->filter('#actu-titre a')->text();
            $link = $node->filter('#actu-titre a')->attr('href');
            $categories = $node->filter('#actu-cat a')->text();

            $crawler = $this->crawle(self::URL . "/{$link}");
            $body = $crawler->filter('.views-field.views-field-body')->text();
            $date = $crawler->filter('#p-date')->text();
            $timestamp = $this->createTimeStamp(
                date: $date,
                format: self::DATE_FORMAT,
                pattern: '/(\d{1}) (\d{2}) (\d{2}) (\d{4}) - (\d{2}:\d{2})/',
                replacement: '$4-$3-$2 $5'
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
}
