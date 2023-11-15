<?php

declare(strict_types=1);

namespace App\Source\Data;

use App\Source\AbstractSource;
use League\Csv\{CannotInsertRecord, Exception, UnavailableStream, Writer};
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Class Politique7sur7Service.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[AsTaggedItem('app.data_source')]
final readonly class Politique7sur7CdSource extends AbstractSource
{
    public const URL = 'https://7sur7.cd';

    public const ID = '7sur7.cd';

    public function supports(string $source): bool
    {
        return $source === self::ID;
    }

    /**
     * @throws UnavailableStream
     * @throws CannotInsertRecord
     * @throws Exception
     */
    public function process(SymfonyStyle $io, int $start, int $end, string $filename = self::ID, ?array $categories = []): void
    {
        $stopwatch = new Stopwatch();
        $category = $categories[0] ?? 'politique';
        $filename = "{$this->projectDir}/data/{$filename}.csv";
        $writer = Writer::createFromPath($this->ensureFileExists($filename), open_mode: 'a+');
        $writer->insertOne(['title', 'link', 'categories', 'body', 'timestamp', 'source']);

        $stopwatch->start('crawling');
        for ($i = $start; $i < $end; $i++) {
            try {
                if ($io->isVerbose()) {
                    $io->info("page {$i}");
                }

                $crawler = $this->crawle(self::URL . "/index.php/category/{$category}?page={$i}");
                $articles = $crawler->filter('.view-content')->children('.row.views-row');
            } catch (\Throwable) {
                continue;
            }

            // loop through the articles and get the title, link, date, categories and body
            $articles->each(function (Crawler $node) use ($writer, $io) {
                try {
                    $categories = ['politique'];
                    $title = $node->filter('.views-field-title a')->text();
                    $link = $node->filter('.views-field-title a')->attr('href');
                    $timestamp = $this->createTimeStamp($node->filter('.views-field-created')->text());

                    try {
                        $body = $this->crawle(self::URL . "/{$link}")->filter('.field.field--name-body')->text();
                    } catch (\Throwable) {
                        $body = '';
                    }

                    $writer->insertOne([$title, $link, implode(',', $categories), $body, $timestamp, self::ID]);
                    if ($io->isVerbose()) {
                        $io->text("> {$title} ✅");
                    }
                } catch (\Throwable) {
                    if ($io->isVerbose()) {
                        $io->text('> failed ❌');
                    }
                    return;
                }
            });
        }

        try {
            $this->dispatchCrawleFinishedEvent($stopwatch, $filename);
        } finally {
            $io->success('Done');
        }
    }
}
