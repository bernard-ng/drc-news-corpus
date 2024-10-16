<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Filesystem;

use App\Aggregator\Domain\Service\Exporter;
use League\Csv\Writer;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Class CsvExporter.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class CsvExporter implements Exporter
{
    public function __construct(
        #[Autowire(value: '%kernel.project_dir%')]
        private string $projectDir
    ) {
    }

    #[\Override]
    public function export(iterable $data): string
    {
        $filename = sprintf('%s/data/export-%s.csv', $this->projectDir, (new \DateTimeImmutable('now'))->format('U'));
        $writer = Writer::createFromPath($filename, open_mode: 'a+');

        if (file_get_contents($filename) === '') {
            $writer->insertOne(['title', 'link', 'categories', 'body', 'source', 'published_at', 'crawled_at']);
        }

        foreach ($data as $article) {
            $writer->insertOne([
                $article->title,
                $article->link,
                $article->categories,
                $article->body,
                $article->source,
                $article->publishedAt->format('Y-m-d H:i:s'),
                $article->crawledAt->format('Y-m-d H:i:s'),
            ]);
        }

        return $filename;
    }
}
