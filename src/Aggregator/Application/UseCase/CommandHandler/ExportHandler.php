<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\CommandHandler;

use App\Aggregator\Application\UseCase\Command\Export;
use App\Aggregator\Domain\Repository\ArticleRepository;

/**
 * Class ExportHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class ExportHandler
{
    public function __construct(
        private ArticleRepository $articleRepository
    ) {
    }

    public function __invoke(Export $command): void
    {
        $articles = $this->articleRepository->export($command->source, $command->date);
    }
}