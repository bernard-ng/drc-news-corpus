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
 * Class PoliticoCdAbstractSource.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[AsTaggedItem('app.data_source')]
final class PoliticoCdSource extends AbstractSource
{
    public const string URL = 'https://politico.cd';

    public const string ID = 'politico.cd';

    private string $category;

    /**
     * @throws \Throwable
     */
    #[\Override]
    public function process(SymfonyStyle $io, ProcessConfig $config): void
    {
        $this->initialize($io, $config->filename);

        $this->category = $config->category ?? 'politique';
        $page = $config->page ?? PageRange::from(sprintf('0:%d', $this->getLastPageNumber(self::URL . "/rubrique/{$this->category}")));

        for ($i = $page->start; $i < $page->end; $i++) {
            try {
                $crawler = $this->crawle(self::URL . "/rubrique/{$this->category}/page/{$i}", $i);
                $articles = $crawler->filter('article.l-post');
            } catch (\Throwable) {
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
            $link = $node->filter('.post-title a')->attr('href');
            $categories = $node->filter('.post-cat a')->text();
            $title = $node->filter('.post-title a')->text();

            $date = $node->filter('time')->attr('datetime');
            $timestamp = $this->createTimeStamp($date, format: 'c');

            if ($interval === null || $interval->inRange((int) $timestamp)) {
                try {
                    $crawler = $this->crawle($link);
                    $body = $crawler->filter('div.post-content.cf.entry-content.content-spacious')->text();
                } catch (\Throwable) {
                    $body = '';
                }

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
