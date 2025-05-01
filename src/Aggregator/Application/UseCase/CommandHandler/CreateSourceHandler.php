<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\CommandHandler;

use App\Aggregator\Application\UseCase\Command\CreateSource;
use App\Aggregator\Domain\Model\Entity\Source;
use App\Aggregator\Domain\Model\Repository\SourceRepository;
use App\SharedKernel\Application\Messaging\CommandHandler;

/**
 * Class AddSourceHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class CreateSourceHandler implements CommandHandler
{
    public function __construct(
        private SourceRepository $sourceRepository
    ) {
    }

    public function __invoke(CreateSource $command): void
    {
        $name = $command->name;
        $source = Source::create($name, "https://{$name}");
        $this->sourceRepository->add($source);
    }
}
