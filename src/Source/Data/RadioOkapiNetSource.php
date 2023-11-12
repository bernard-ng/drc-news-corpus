<?php

declare(strict_types=1);

namespace App\Source\Data;

use App\Source\AbstractSource;
use League\Csv\UnavailableStream;
use League\Csv\Writer;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Class RadioOkapiNetSource.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[AsTaggedItem('app.data_source')]
final readonly class RadioOkapiNetSource extends AbstractSource
{
    public const URL = 'https://www.radiookapi.net';

    public const ID = 'radiookapi.net';

    public function supports(string $source): bool
    {
        return $source === self::ID;
    }

    /**
     * @throws UnavailableStream
     */
    public function process(SymfonyStyle $io, int $start, int $end, string $filename = self::ID, ?array $categories = []): void
    {
        $stopwatch = new Stopwatch();
        $filename = "{$this->projectDir}/data/{$filename}.csv";
        $writer = Writer::createFromPath($this->ensureFileExists($filename), open_mode: 'a+');

        $stopwatch->start('crawling');
        for ($i = $start; $i < $end; $i++) {
            try {
                $io->info("page {$i}");
                $crawler = $this->crawle(self::URL . "/actualite?page={$i}");
                $articles = $crawler->filter('.view-content')->children('.views-row.content-row');
                $writer->insertOne(['title', 'link', 'categories', 'body', 'timestamp', 'source']);
            } catch (\Throwable) {
                continue;
            }

            // loop through the articles and get the title, link, date, categories and body
            $articles->each(function (Crawler $node) use ($writer, $io) {
                try {
                    $categories = $node->filter('.views-field-field-cat-gorie a')->each(fn (Crawler $node) => $node->text());
                    $title = $node->filter('.views-field-title a')->text();
                    $link = $node->filter('.views-field-title a')->attr('href');
                    $timestamp = $this->createTimeStamp($node->filter('.views-field-created')->text());

                    try {
                        $body = $this->crawle(self::URL . "/{$link}")->filter('.field-name-body')->text();
                    } catch (\Throwable) {
                        $body = '';
                    }

                    $writer->insertOne([$title, $link, implode(',', $categories), $body, $timestamp, self::ID]);
                    $io->text("> {$title} ✅");
                } catch (\Throwable) {
                    $io->text('> failed ❌');
                    return;
                }
            });
            $stopwatch->lap('crawling');
        }

        try {
            $this->dispatchCrawleFinishedEvent($stopwatch, $filename);
        } finally {
            $io->success('Done');
        }
    }
}
