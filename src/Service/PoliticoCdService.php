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
 * Class PoliticoCdService.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class PoliticoCdService extends AbstractService
{
    public const URL = 'https://7sur7.cd';

    /**
     * @throws UnavailableStream
     */
    public function process(int $start, int $end, string $filename = '', ?array $categories = []): void
    {
        $categories = $categories[0] ?? 'politique';
        $writer = Writer::createFromPath("{$this->projectDir}/data/7sur7{$filename}.csv", open_mode: 'a+');

        for ($i = $start; $i < $end; $i++) {
            try {
                // crawl the website and get the content of the page
                $crawler = new Crawler($this->client->request('GET', sprintf("%s/index.php/category/$categories?page=$i", self::URL))->getContent());
                $articles = $crawler->filter('.view-content')->children('.row.views-row');
            } catch (\Throwable) {
                continue;
            }

            if ($articles->count() <= 0) {
                continue;
            }

            // loop through the articles and get the title, link, date, categories and body
            $articles->each(function(Crawler $node) use ($writer, $categories) {
                $title = $node->filter('.views-field-title a')->text();
                $link = $node->filter('.views-field-title a')->attr('href');
                $date = (\DateTime::createFromFormat('D d/m/Y - H:m', $node->filter('.views-field-created')
                    ->text()))
                    ->format('U');

                try {
                    // get the body of the article
                    $body = (new Crawler($this->client->request('GET', sprintf("%s/$link", self::URL))->getContent()))
                        ->filter('.field.field--name-body')
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
