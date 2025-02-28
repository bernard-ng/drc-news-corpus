<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\CommandHandler;

use App\Aggregator\Application\UseCase\Command\Clear;
use App\Aggregator\Domain\Repository\ArticleRepository;
use App\SharedKernel\Application\Bus\CommandHandler;

/**
 * Class ClearHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class ClearHandler implements CommandHandler
{
    public function __construct(
        private ArticleRepository $articleRepository,
    ) {
    }

    public function __invoke(Clear $command): int
    {
        return $this->articleRepository->clear($command->source, $command->category);
    }
}
