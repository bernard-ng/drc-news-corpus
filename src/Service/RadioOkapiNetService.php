<?php

declare(strict_types=1);

namespace App\Service;

use League\Csv\Writer;
use League\Csv\UnavailableStream;
use Symfony\Component\Mime\Email;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchPeriod;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Class OkapiService.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class RadioOkapiNetService extends AbstractService
{
    public const URL = 'https://www.radiookapi.net';

    /**
     * @throws UnavailableStream|TransportExceptionInterface
     */
    public function process(int $start, int $end, string $filename, ?array $categories = []): void
    {
        $stopwatch = new Stopwatch();
        $writer = Writer::createFromPath("{$this->projectDir}/data/{$filename}.csv", open_mode: 'a+');

        $stopwatch->start('crawling');
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
            $stopwatch->lap("page $i");
        }

        try {
            $event = $stopwatch->stop('crawling');
            $periods = join("\n", array_map(fn (StopwatchPeriod $period) => (string) $period, $event->getPeriods()));


            $email = (new Email())
                ->from('contact@devscast.tech', 'Devscast')
                ->to('ngandubernard@gmail.com', 'Bernard Ngandu')
                ->subject('radiookapi.net Crawling done')
                ->text(
                    <<<EOF
                        The crawling of radiookapi.net is done.
                        It took {$event}
                        Here are the details:
                        
                        $periods
                    EOF
                )
                ->attachFromPath("{$this->projectDir}/data/{$filename}.csv");
            $this->mailer->send($email);
        } finally {
            $this->io->success('Done');
        }
    }
}
