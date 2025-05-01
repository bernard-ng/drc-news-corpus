<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\CommandHandler;

use App\Aggregator\Application\UseCase\Command\DeleteArticles;
use App\Aggregator\Domain\Model\Repository\ArticleRepository;
use App\SharedKernel\Application\Messaging\CommandHandler;

/**
 * Class DeleteArticlesHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class DeleteArticlesHandler implements CommandHandler
{
    public function __construct(
        private ArticleRepository $articleRepository,
    ) {
    }

    public function __invoke(DeleteArticles $command): int
    {
        return $this->articleRepository->clear($command->source, $command->category);
    }
}
