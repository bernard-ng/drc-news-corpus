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
final class Politique7sur7CdSource extends AbstractSource
{
    public const string URL = 'https://7sur7.cd';

    public const string ID = '7sur7.cd';

    private string $category;

    /**
     * @throws \Throwable
     */
    #[\Override]
    public function process(SymfonyStyle $io, ProcessConfig $config): void
    {
        $this->initialize($io, $config->filename);
        $this->category = $config->category ?? 'politique';
        $page = $config->page ?? PageRange::from(
            sprintf('0:%d', $this->getLastPageNumber(self::URL . "/index.php/category/{$this->category}"))
        );

        for ($i = $page->start; $i < $page->end; $i++) {
            try {
                $crawler = $this->crawle(self::URL . "/index.php/category/{$this->category}?page={$i}", $i);
                $articles = $crawler->filter('.view-content')->children('.row.views-row');
            } catch (\Throwable) {
                continue;
            }

            $articles->each(fn (Crawler $node) => $this->processNode($node, $config->date));
        }

        $this->onCrawlingCompleted();
    }

    /**
     * @throws \Throwable
     */
    #[\Override]
    public function processNode(Crawler $node, ?DateRange $interval = null): void
    {
        try {
            $date = $node->filter('.views-field-created')->text();
            $timestamp = $this->createTimeStamp(
                date: $date,
                format: self::DATE_FORMAT,
                pattern: '/\w{3} (\d{2})\/(\d{2})\/(\d{4}) - (\d{2}:\d{2})/',
                replacement: '$3-$2-$1 $4'
            );
            $title = $node->filter('.views-field-title a')->text();
            $link = $node->filter('.views-field-title a')->attr('href');

            if ($interval === null || $interval->inRange((int) $timestamp)) {
                try {
                    $body = $this->crawle(self::URL . "/{$link}")->filter('.field.field--name-body')->text();
                } catch (\Throwable) {
                    $body = '';
                }

                $this->writeOnFile($title, $link, $this->category, $body, $timestamp);
            } else {
                $this->skipOnOutOfRange($interval, $timestamp, $title, $date);
            }
        } catch (\Throwable $e) {
            $this->skipOnError($e);
            return;
        }
    }
}
