<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Source;

use App\Aggregator\Domain\Service\SourceFetcher as SourceFetcherInterface;
use App\Aggregator\Domain\ValueObject\DateRange;
use App\Aggregator\Domain\ValueObject\FetchConfig;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

/**
 * Class SourceFetcher.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class SourceFetcher implements SourceFetcherInterface
{
    /**
     * @var iterable<SourceFetcherInterface>
     */
    private iterable $sources;

    public function __construct(
        #[AutowireIterator('app.data_source')]
        \Traversable $sources
    ) {
        $this->sources = iterator_to_array($sources);
    }

    #[\Override]
    public function fetch(FetchConfig $config): void
    {
        foreach ($this->sources as $source) {
            if ($source->supports($config->id)) {
                $source->fetch($config);
            }
        }
    }

    #[\Override]
    public function supports(string $source): bool
    {
        return true;
    }

    #[\Override]
    public function fetchOne(string $html, ?DateRange $interval = null): void
    {
        throw new \RuntimeException('Not implemented');
    }
}
