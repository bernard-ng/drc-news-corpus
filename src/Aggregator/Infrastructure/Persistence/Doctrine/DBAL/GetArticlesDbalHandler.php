<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL;

use App\Aggregator\Application\ReadModel\Article;
use App\Aggregator\Application\ReadModel\Articles;
use App\Aggregator\Application\UseCase\Query\GetArticlesQuery;
use App\Aggregator\Application\UseCase\QueryHandler\GetArticlesHandler;
use App\Aggregator\Domain\ValueObject\Filters\ArticleFilters;
use App\SharedKernel\Domain\Model\ValueObject\Pagination;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\Mapping;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\NoResult;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class GetArticlesDbalHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetArticlesDbalHandler implements GetArticlesHandler
{
    public function __construct(
        private Connection $connection,
        private PaginatorInterface $paginator
    ) {
    }

    #[\Override]
    public function __invoke(GetArticlesQuery $query): Articles
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('id, title, link, categories, body, source, hash, published_at, crawled_at')
            ->from('article')
            ->orderBy('published_at', 'DESC');

        $qb = $this->applyFilters($qb, $query->filters);

        try {
            /** @var SlidingPaginationInterface<int, array<string, mixed>> $data */
            $data = $this->paginator->paginate($qb, $query->page->page, $query->page->limit);
        } catch (\Throwable $e) {
            throw NoResult::forQuery($qb->getSQL(), $qb->getParameters(), $e);
        }

        return $this->mapArticles($data);
    }

    /**
     * @param SlidingPaginationInterface<int, array<string, mixed>> $data
     */
    private function mapArticles(SlidingPaginationInterface $data): Articles
    {
        return new Articles(
            items: array_map(
                fn ($item) => new Article(
                    Mapping::uuid($item, 'id'),
                    Mapping::string($item, 'title'),
                    $this->createAbsoluteUri(
                        link: Mapping::string($item, 'link'),
                        source: Mapping::string($item, 'source')
                    ),
                    Mapping::string($item, 'categories'),
                    Mapping::string($item, 'body'),
                    Mapping::string($item, 'source'),
                    Mapping::string($item, 'hash'),
                    Mapping::datetime($item, 'published_at'),
                    Mapping::datetime($item, 'crawled_at')
                ),
                \iterator_to_array($data->getItems())
            ),
            pagination: Pagination::create($data->getPaginationData())
        );
    }

    private function applyFilters(QueryBuilder $qb, ArticleFilters $filters): QueryBuilder
    {
        if ($filters->source !== null) {
            $qb->andWhere('source = :source')
                ->setParameter('source', $filters->source);
        }

        if ($filters->category !== null) {
            $qb->andWhere('categories LIKE :category')
                ->setParameter('category', "%{$filters->category}%");
        }

        if ($filters->search !== null) {
            $qb->andWhere('title LIKE :search OR body LIKE :search')
                ->setParameter('search', "%{$filters->search}%");
        }

        if ($filters->dateRange !== null) {
            $qb->andWhere('published_at BETWEEN FROM_UNIXTIME(:start) AND FROM_UNIXTIME(:end)')
                ->setParameter('start', $filters->dateRange->start, ParameterType::INTEGER)
                ->setParameter('end', $filters->dateRange->end, ParameterType::INTEGER);
        }

        return $qb;
    }

    private function createAbsoluteUri(string $link, string $source): string
    {
        if (str_starts_with($link, 'http')) {
            return $link;
        }

        return sprintf('https://%s/%s', $source, trim($link, '/'));
    }
}
