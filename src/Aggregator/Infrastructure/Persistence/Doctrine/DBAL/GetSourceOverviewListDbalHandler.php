<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL;

use App\Aggregator\Application\ReadModel\Source\SourceOverviewList;
use App\Aggregator\Application\UseCase\Query\GetSourceOverviewList;
use App\Aggregator\Application\UseCase\QueryHandler\GetSourceOverviewListHandler;
use App\Aggregator\Infrastructure\Persistence\Doctrine\CacheKey\SourceCacheKey;
use App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL\Features\SourceQuery;
use App\IdentityAndAccess\Domain\Model\Identity\UserId;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\NoResult;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class GetSourceOverviewListDbalHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetSourceOverviewListDbalHandler implements GetSourceOverviewListHandler
{
    use SourceQuery;

    public function __construct(
        private Connection $connection,
        private PaginatorInterface $paginator,
    ) {
    }

    #[\Override]
    public function __invoke(GetSourceOverviewList $query): SourceOverviewList
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                's.name as source_name',
                's.description as source_description',
                's.url as source_url',
                's.updated_at as source_updated_at',
                's.display_name as source_display_name',
                's.bias as source_bias',
                's.reliability as source_reliability',
                's.transparency as source_transparency',
                "CONCAT('https://devscast.org/images/sources/', s.name, '.png') as source_image",
                'COUNT(a.hash) AS articles_count',
                'MAX(a.crawled_at) AS source_crawled_at',
                'COUNT(CASE WHEN a.metadata IS NOT NULL THEN 1 ELSE NULL END) AS articles_metadata_available',
            )
            ->from('article', 'a')
            ->innerJoin('a', 'source', 's', 'a.source = s.name')
            ->groupBy('s.name')
            ->orderBy('articles_count', 'DESC')
            ->enableResultCache(new QueryCacheProfile(3600, SourceCacheKey::SOURCE_OVERVIEW_LIST->value))
        ;

        if ($query->userId instanceof UserId) {
            $qb->addSelect(sprintf('%s as source_is_followed', $this->isSourceFollowedQuery()))
                ->setParameter('userId', $query->userId->toBinary(), ParameterType::BINARY)
                ->disableResultCache();
        }

        try {
            /** @var SlidingPaginationInterface<int, array<string, mixed>> $data */
            $data = $this->paginator->paginate($qb, $query->page->page, $query->page->limit);
        } catch (\Throwable $e) {
            throw NoResult::forQuery($qb->getSQL(), $qb->getParameters(), $e);
        }

        return SourceOverviewList::create($data->getItems(), $data->getPaginationData());
    }
}
