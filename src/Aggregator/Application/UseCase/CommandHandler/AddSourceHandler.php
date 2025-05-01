<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\CommandHandler;

use App\Aggregator\Application\UseCase\Command\AddSource;
use App\Aggregator\Domain\Model\Entity\Source;
use App\Aggregator\Domain\Model\Repository\SourceRepository;
use App\SharedKernel\Application\Bus\CommandHandler;

/**
 * Class AddSourceHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class AddSourceHandler implements CommandHandler
{
    public function __construct(
        private SourceRepository $sourceRepository
    ) {
    }

    public function __invoke(AddSource $command): void
    {
        $name = $command->name;
        $source = Source::create($name, "https://{$name}");
        $this->sourceRepository->add($source);
    }
}
