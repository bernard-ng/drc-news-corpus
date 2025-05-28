<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Crawler\Source;

use App\Aggregator\Domain\Model\ValueObject\Crawling\CrawlingSettings;
use App\Aggregator\Domain\Service\Crawling\SourceCrawler as SourceCrawlerInterface;
use App\SharedKernel\Domain\Model\ValueObject\DateRange;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

/**
 * Class SourceFetcher.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class SourceCrawler implements SourceCrawlerInterface
{
    /**
     * @var iterable<SourceCrawlerInterface>
     */
    private iterable $sources;

    public function __construct(
        #[AutowireIterator('app.data_source')] \Traversable $sources
    ) {
        $this->sources = iterator_to_array($sources);
    }

    #[\Override]
    public function fetch(CrawlingSettings $settings): void
    {
        foreach ($this->sources as $source) {
            if ($source->supports($settings->id)) {
                $source->fetch($settings);
            }
        }
    }

    #[\Override]
    public function supports(string $source): bool
    {
        return true;
    }

    #[\Override]
    public function fetchOne(string $html, ?DateRange $dateRange = null): void
    {
        throw new \RuntimeException('Not implemented');
    }

    public function get(string $id): Source
    {
        /** @var Source $source */
        foreach ($this->sources as $source) {
            if ($source->supports($id)) {
                return $source;
            }
        }

        throw new \RuntimeException('Source not found');
    }
}
