<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\CommandHandler;

use App\Aggregator\Application\UseCase\Command\CreateArticle;
use App\Aggregator\Domain\Entity\Article;
use App\Aggregator\Domain\Exception\DuplicateArticle;
use App\Aggregator\Domain\Repository\ArticleRepository;
use App\SharedKernel\Application\Bus\CommandHandler;
use App\SharedKernel\Domain\Model\IdGenerator;

/**
 * Class CreateArticleHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class CreateArticleHandler implements CommandHandler
{
    public function __construct(
        private IdGenerator $idGenerator,
        private ArticleRepository $articleRepository
    ) {
    }

    public function __invoke(CreateArticle $command): string
    {
        $article = $this->articleRepository->getByLink($command->link);
        if ($article !== null) {
            throw DuplicateArticle::withLink($command->link);
        }

        $article = new Article(
            id: $this->idGenerator->uuid(),
            title: $command->title,
            link: $command->link,
            categories: $command->categories,
            body: $command->body,
            source: $command->source,
            publishedAt: $command->timestamp,
            crawledAt: (int) (new \DateTimeImmutable('now'))->format('U')
        );
        $this->articleRepository->add($article);

        return $article->id;
    }
}
