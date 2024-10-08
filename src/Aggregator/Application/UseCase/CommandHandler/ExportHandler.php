<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\CommandHandler;

use App\Aggregator\Application\UseCase\Command\Export;
use App\Aggregator\Domain\Repository\ArticleRepository;
use App\Aggregator\Domain\Service\Exporter;
use App\SharedKernel\Application\Bus\CommandHandler;

/**
 * Class ExportHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class ExportHandler implements CommandHandler
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private Exporter $exporter
    ) {
    }

    public function __invoke(Export $command): void
    {
        $articles = $this->articleRepository->export($command->source, $command->date);
        $this->exporter->export($articles);
    }
}
