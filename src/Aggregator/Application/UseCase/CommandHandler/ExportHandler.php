<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\CommandHandler;

use App\Aggregator\Application\ReadModel\ExportedArticle;
use App\Aggregator\Application\UseCase\Command\Export;
use App\Aggregator\Application\UseCase\Query\ExportQuery;
use App\SharedKernel\Application\Bus\CommandHandler;
use App\SharedKernel\Application\Bus\QueryBus;
use App\SharedKernel\Domain\DataTransfert\DataExporter;
use App\SharedKernel\Domain\DataTransfert\TransfertSetting;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Class ExportHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class ExportHandler implements CommandHandler
{
    public function __construct(
        private QueryBus $queryBus,
        private DataExporter $exporter,
        #[Autowire(param: 'kernel.project_dir')]
        private string $projectDir
    ) {
    }

    public function __invoke(Export $command): void
    {
        $filename = sprintf(
            '%s/data/export-%s.csv',
            $this->projectDir,
            (new \DateTimeImmutable('now'))->format('U')
        );

        /** @var iterable<ExportedArticle> $articles */
        $articles = $this->queryBus->handle(new ExportQuery($command->source, $command->date));

        $this->exporter->export($articles, new TransfertSetting($filename));
    }
}
