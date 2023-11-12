<?php

declare(strict_types=1);

namespace App\Service;

use League\Csv\Writer;
use League\Csv\UnavailableStream;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Class OkapiService.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class RadioOkapiNetService extends AbstractService
{
    public const URL = 'https://www.radiookapi.net';

    /**
     * @throws UnavailableStream
     */
    public function process(int $start, int $end, string $filename, ?array $categories = []): void
    {
        $writer = Writer::createFromPath("{$this->projectDir}/data/{$filename}.csv", open_mode: 'a+');

        for ($i = $start; $i < $end; $i++) {
            $this->io->info("page $i");

            try {
                // crawl the website and get the content of the page
                $crawler = new Crawler($this->client->request('GET', sprintf("%s/actualite?page=$i", self::URL))->getContent());
                $articles = $crawler->filter('.view-content')->children('.views-row.content-row');
            } catch (\Throwable) {
                continue;
            }

            // loop through the articles and get the title, link, date, categories and body
            $articles->each(function(Crawler $node) use ($writer) {
                try {
                    $categories = $node->filter('.views-field-field-cat-gorie a')->each(fn (Crawler $node) => $node->text());
                    $title = $node->filter('.views-field-title a')->text();
                    $link = $node->filter('.views-field-title a')->attr('href');
                    $date = (\DateTime::createFromFormat('d/m/Y - H:m', $node->filter('.views-field-created')->text()))->format('U');
                } catch (\Throwable) {
                    return;
                }

                try {
                    // get the body of the article
                    $body = (new Crawler($this->client->request('GET', sprintf("%s/$link", self::URL))->getContent()))
                        ->filter('.field-name-body')
                        ->text();
                } catch (\Throwable) {
                    $body = '';
                }

                try {
                    // write the data to the csv file
                    $writer->insertOne([$title, $link, implode(',', $categories), $body, $date]);
                    $this->io->text("> $title ✅");
                } catch (\Throwable) {
                    $this->io->text("> $title ❌");
                }
            });
        }
    }
}
