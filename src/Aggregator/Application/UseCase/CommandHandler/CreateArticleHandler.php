<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\CommandHandler;

use App\Aggregator\Application\UseCase\Command\CreateArticle;
use App\Aggregator\Domain\Exception\DuplicatedArticle;
use App\Aggregator\Domain\Model\Entity\Article;
use App\Aggregator\Domain\Model\Repository\ArticleRepository;
use App\Aggregator\Domain\Model\Repository\SourceRepository;
use App\Aggregator\Domain\Service\HashCalculator;
use App\SharedKernel\Application\Messaging\CommandHandler;

/**
 * Class CreateArticlesHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class CreateArticleHandler implements CommandHandler
{
    public function __construct(
        private SourceRepository $sourceRepository,
        private ArticleRepository $articleRepository,
        private HashCalculator $hashCalculator
    ) {
    }

    public function __invoke(CreateArticle $command): void
    {
        $hash = $this->hashCalculator->calculate($command->link);
        $article = $this->articleRepository->getByHash($hash);
        if ($article !== null) {
            throw DuplicatedArticle::withLink($command->link);
        }

        /** @var \DateTimeImmutable $publishedAt */
        $publishedAt = \DateTimeImmutable::createFromFormat('U', (string) $command->timestamp);
        $source = $this->sourceRepository->getByName($command->source);

        $article = new Article(
            title: $command->title,
            link: $this->createAbsoluteUri($command->link, $command->source),
            body: $command->body,
            hash: $hash,
            categories: mb_strtolower($command->categories),
            source: $source,
            publishedAt: $publishedAt,
        );
        $this->articleRepository->add($article);
    }

    private function createAbsoluteUri(string $link, string $source): string
    {
        if (str_starts_with($link, 'http')) {
            return $link;
        }

        return sprintf('https://%s/%s', $source, trim($link, '/'));
    }
}
