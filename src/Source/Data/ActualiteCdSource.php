<?php

declare(strict_types=1);

namespace App\Source\Data;

use App\Source\AbstractSource;
use League\Csv\{Exception, UnavailableStream, Writer};
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
final readonly class ActualiteCdSource extends AbstractSource
{
    public const URL = 'https://actualite.cd';

    public const ID = 'actualite.cd';

    /**
     * @throws UnavailableStream
     * @throws Exception
     */
    public function process(SymfonyStyle $io, int $start, int $end, string $filename = self::ID, ?array $categories = []): void
    {
        $stopwatch = new Stopwatch();
        $filename = "{$this->projectDir}/data/{$filename}.csv";
        $writer = Writer::createFromPath($this->ensureFileExists($filename), open_mode: 'a+');
        $writer->insertOne(['title', 'link', 'categories', 'body', 'timestamp', 'source']);

        $stopwatch->start('crawling');
        for ($i = $start; $i < $end; $i++) {
            try {
                if ($io->isVerbose()) {
                    $io->info("page {$i}");
                }

                $crawler = $this->crawle(self::URL . "/actualite?page={$i}");
                $articles = $crawler->filter('#views-bootstrap-taxonomy-term-page-2 > div > div');
            } catch (\Throwable $e) {
                continue;
            }

            // loop through the articles and get the title, link, date, categories and body
            $articles->each(function (Crawler $node) use ($writer, $io) {
                try {
                    $title = $node->filter('#actu-titre a')->text();
                    $link = $node->filter('#actu-titre a')->attr('href');
                    $categories = $node->filter('#actu-cat a')->text();

                    try {
                        $crawler = $this->crawle(self::URL . "/{$link}");
                        $body = $crawler->filter('.views-field.views-field-body')->text();
                        $timestamp = $this->createTimeStamp($crawler->filter('#p-date')->text(),'l d F Y - H:i');
                    } catch (\Throwable) {
                        $body = '';
                        $timestamp = '';
                    }

                    $writer->insertOne([$title, $link, $categories, $body, $timestamp, self::ID]);
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
