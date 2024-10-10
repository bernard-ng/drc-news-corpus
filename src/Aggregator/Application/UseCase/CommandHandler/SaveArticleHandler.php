<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\CommandHandler;

use App\Aggregator\Application\UseCase\Command\SaveArticle;
use App\Aggregator\Domain\Entity\Article;
use App\Aggregator\Domain\Exception\DuplicatedArticle;
use App\Aggregator\Domain\Repository\ArticleRepository;
use App\SharedKernel\Application\Bus\CommandHandler;
use App\SharedKernel\Domain\Model\IdGenerator;

/**
 * Class SaveArticleHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class SaveArticleHandler implements CommandHandler
{
    public function __construct(
        private IdGenerator $idGenerator,
        private ArticleRepository $articleRepository
    ) {
    }

    public function __invoke(SaveArticle $command): string
    {
        $hash = md5($command->link);
        $article = $this->articleRepository->getByHash($hash);
        if ($article !== null) {
            throw DuplicatedArticle::withLink($command->link);
        }

        /** @var \DateTimeImmutable $publishedAt */
        $publishedAt = \DateTimeImmutable::createFromFormat('U', (string) $command->timestamp);
        $createdAt = new \DateTimeImmutable('now');

        $article = new Article(
            id: $this->idGenerator->uuid(),
            title: $command->title,
            link: $command->link,
            categories: $command->categories,
            body: $command->body,
            source: $command->source,
            hash: $hash,
            publishedAt: $publishedAt,
            crawledAt: $createdAt
        );
        $this->articleRepository->add($article);

        return $article->id;
    }
}
