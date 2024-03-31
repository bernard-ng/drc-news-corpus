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
 * Class RadioOkapiNetSource.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[AsTaggedItem('app.data_source')]
final class RadioOkapiNetSource extends AbstractSource
{
    public const string URL = 'https://www.radiookapi.net';

    public const string ID = 'radiookapi.net';

    /**
     * @throws \Throwable
     */
    #[\Override]
    public function process(SymfonyStyle $io, ProcessConfig $config): void
    {
        $this->initialize($io, $config->filename);
        $page = $config->page ?? PageRange::from(sprintf('0:%d', $this->getLastPageNumber(self::URL . '/actualite')));

        for ($i = $page->start; $i <= $page->end; $i++) {
            try {
                $crawler = $this->crawle(self::URL . "/actualite?page={$i}", $i);
                $articles = $crawler->filter('.view-content')->children('.views-row.content-row');
            } catch (\Throwable) {
                continue;
            }

            $articles->each(fn (Crawler $node) => $this->processNode($node, $config->date));
        }

        $this->onCrawlingCompleted($io);
    }

    #[\Override]
    public function processNode(Crawler $node, ?DateRange $interval = null): void
    {
        try {
            $date = $node->filter('.views-field-created')->text();
            $timestamp = $this->createTimeStamp(
                date: $date,
                format: self::DATE_FORMAT,
                pattern: '/(\d{2})\/(\d{2})\/(\d{4}) - (\d{2}:\d{2})/',
                replacement: '$3-$2-$1 $4'
            );
            $categories = $node->filter('.views-field-field-cat-gorie a')->each(fn (Crawler $node) => $node->text());
            $title = $node->filter('.views-field-title a')->text();
            $link = $node->filter('.views-field-title a')->attr('href');

            if ($interval === null || $interval->inRange((int) $timestamp)) {
                try {
                    $body = $this->crawle(self::URL . "/{$link}")->filter('.field-name-body')->text();
                } catch (\Throwable) {
                    $body = '';
                }

                $this->writeOnFile($title, $link, implode(',', $categories), $body, $timestamp);
            } else {
                $this->skipOnOutOfRange($interval, $timestamp, $title, $date);
            }
        } catch (\Throwable $e) {
            $this->skipOnError($e);
            return;
        }
    }
}
