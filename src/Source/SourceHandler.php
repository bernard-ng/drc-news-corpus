<?php

declare(strict_types=1);

namespace App\Source;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

/**
 * Class SourceHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class SourceHandler
{
    /**
     * @var iterable<SourceInterface>
     */
    private iterable $sources;

    public function __construct(
        #[TaggedIterator('app.data_source')] \Traversable $sources
    ) {
        $this->sources = iterator_to_array($sources);
    }

    public function process(string $id, SymfonyStyle $io, int $start, int $end, string $filename, ?array $categories = []): bool
    {
        $sourceSupported = false;
        foreach ($this->sources as $source) {
            if ($source->supports($id)) {
                $sourceSupported = true;
                $source->process($io, $start, $end, $filename, $categories);
            }
        }

        return $sourceSupported;
    }
}
