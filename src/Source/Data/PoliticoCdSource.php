<?php

declare(strict_types=1);

namespace App\Source\Data;

use App\Source\AbstractSource;
use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\UnavailableStream;
use League\Csv\Writer;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Class PoliticoCdAbstractSource.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[AsTaggedItem('app.data_source')]
final readonly class PoliticoCdSource extends AbstractSource
{
    public const URL = 'https://politico.cd';

    public const ID = 'politico.cd';

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
        $filename = "{$this->projectDir}/data/{$filename}.csv";
        $category = $categories[0] ?? 'encontinu';
        $writer = Writer::createFromPath($this->ensureFileExists($filename), open_mode: 'a+');
        $writer->insertOne(['title', 'link', 'categories', 'body', 'timestamp', 'source']);

        $stopwatch->start('crawling');
        for ($i = $start; $i < $end; $i++) {
            try {
                if ($io->isVerbose()) {
                    $io->info("page {$i}");
                }

                $crawler = $this->crawle(self::URL . "/rubrique/{$category}/page/{$i}");
                $articles = $crawler->filter('article.l-post');
            } catch (\Throwable) {
                continue;
            }

            // loop through the articles and get the title, link, date, categories and body
            $articles->each(function (Crawler $node) use ($writer, $io) {
                try {
                    /** @var string $link */
                    $link = $node->filter('.post-title a')->attr('href');
                    $categories = $node->filter('.post-cat a')->text();
                    $title = $node->filter('.post-title a')->text();

                    /** @var string $date */
                    $date = $node->filter('time')->attr('datetime');
                    $timestamp = $this->createTimeStamp($date, format: 'c');

                    try {
                        $crawler = $this->crawle($link);
                        $body = $crawler->filter('div.post-content.cf.entry-content.content-spacious')->text();
                    } catch (\Throwable) {
                        $body = '';
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
