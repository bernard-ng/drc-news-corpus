<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL;

use App\Aggregator\Application\ReadModel\ArticleOverviewList;
use App\Aggregator\Application\UseCase\Query\GetArticleOverviewList;
use App\Aggregator\Application\UseCase\QueryHandler\GetArticleOverviewListHandler;
use App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL\Features\ArticleQuery;
use App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL\Features\BookmarkQuery;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\Features\PaginationQuery;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\NoResult;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;

/**
 * Class GetArticleOverviewListDbalHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetArticleOverviewListDbalHandler implements GetArticleOverviewListHandler
{
    use PaginationQuery;
    use BookmarkQuery;
    use ArticleQuery;

    public function __construct(
        private Connection $connection,
    ) {
    }

    #[\Override]
    public function __invoke(GetArticleOverviewList $query): ArticleOverviewList
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'a.id as article_id',
                'a.title as article_title',
                'a.link as article_link',
                'a.categories as article_categories',
                'a.excerpt as article_excerpt',
                'a.published_at as article_published_at',
                'a.image as article_image',
                'a.reading_time as article_reading_time',
            )
            ->addSelect(
                's.display_name as source_display_name',
                "CONCAT('https://devscast.org/images/sources/', s.name, '.png') as source_image",
                's.url as source_url',
                's.name as source_name',
            )
            ->addSelect(sprintf('%s as article_is_bookmarked', $this->isArticleBookmarkedQuery()))
            ->from('article', 'a')
            ->innerJoin('a', 'source', 's', 'a.source = s.name')
            ->setParameter('userId', $query->userId->toBinary(), ParameterType::BINARY)
            ->orderBy('a.published_at', 'DESC')
        ;

        $qb = $this->applyArticleFilters($qb, $query->filters);
        $qb = $this->applyCursorPagination($qb, $query->page, 'a.id', $this->getArticleLastId(...));

        try {
            /** @var array<string, mixed> $data */
            $data = $qb->executeQuery()->fetchAllAssociative();
        } catch (\Throwable $e) {
            throw NoResult::forQuery($qb->getSQL(), $qb->getParameters(), $e);
        }

        $pagination = $this->getPagination($data, $query->page, 'article_id');
        return ArticleOverviewList::create($data, $pagination);
    }
}
