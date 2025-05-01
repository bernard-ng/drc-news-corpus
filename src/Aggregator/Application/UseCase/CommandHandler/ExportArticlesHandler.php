<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\CommandHandler;

use App\Aggregator\Application\ReadModel\ArticleForExport;
use App\Aggregator\Application\UseCase\Command\ExportArticles;
use App\Aggregator\Application\UseCase\Query\GetArticlesForExport;
use App\SharedKernel\Application\Messaging\CommandHandler;
use App\SharedKernel\Application\Messaging\QueryBus;
use App\SharedKernel\Domain\DataTransfert\DataExporter;
use App\SharedKernel\Domain\DataTransfert\TransfertSetting;

/**
 * Class GetArticlesForExportHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class ExportArticlesHandler implements CommandHandler
{
    public function __construct(
        private QueryBus $queryBus,
        private DataExporter $exporter,
        private string $projectDir
    ) {
    }

    public function __invoke(ExportArticles $command): void
    {
        $filename = sprintf(
            '%s/data/export-%s.csv',
            $this->projectDir,
            new \DateTimeImmutable('now')->format('U')
        );

        /** @var iterable<ArticleForExport> $articles */
        $articles = $this->queryBus->handle(new GetArticlesForExport($command->source, $command->date));

        $this->exporter->export($articles, new TransfertSetting($filename));
    }
}
