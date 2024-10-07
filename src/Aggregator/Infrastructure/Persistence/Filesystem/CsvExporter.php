<?php

declare(strict_types=1);

namespace  App\Aggregator\Infrastructure\Persistence\Filesystem;

use League\Csv\Writer;
use App\Aggregator\Domain\Service\Exporter;

/**
 * Class CsvExporter.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class CsvExporter implements Exporter
{
    protected Writer $writer;
    protected string $filename;

    public function __construct(
        private string $projectDir
    ) {
    }

    #[\Override]
    public function export(array $data): string
    {
        $filename = sprintf("%s/data/export-%s.csv", $this->projectDir, (new \DateTimeImmutable('now'))->format('U'));
        $writer = Writer::createFromPath($filename, open_mode: 'a+');

        if (!file_exists($filename)) {
            $writer->insertOne(['title', 'link', 'categories', 'body', 'published_at', 'source']);
        }

        foreach ($data as $article) {
            $this->writer->insertOne([
                $article->title,
                $article->link,
                $article->categories,
                $article->body,
                $article->publishedAt,
                $article->source
            ]);
        }

        return $filename;
    }
}
