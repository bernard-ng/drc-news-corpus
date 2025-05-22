<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL;

use App\Aggregator\Application\ReadModel\Source\SourceOverview;
use App\Aggregator\Application\ReadModel\Source\SourceOverviewList;
use App\Aggregator\Application\UseCase\Query\GetSourceOverviewList;
use App\Aggregator\Application\UseCase\QueryHandler\GetSourceOverviewListHandler;
use App\Aggregator\Infrastructure\Persistence\Doctrine\CacheKey\SourceCacheKey;
use App\IdentityAndAccess\Domain\Model\Identity\UserId;
use App\SharedKernel\Application\Asset\AssetType;
use App\SharedKernel\Domain\Model\ValueObject\Pagination;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\Mapping;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\NoResult;
use App\SharedKernel\Infrastructure\Persistence\Filesystem\Asset\AssetUrlProvider;
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
    public function __construct(
        private Connection $connexion,
        private PaginatorInterface $paginator,
        private AssetUrlProvider $assetUrlProvider
    ) {
    }

    #[\Override]
    public function __invoke(GetSourceOverviewList $query): SourceOverviewList
    {
        $qb = $this->connexion->createQueryBuilder()
            ->select(
                's.name as source_name',
                's.description as source_description',
                's.url as source_url',
                's.updated_at as source_updated_at',
                's.display_name as source_display_name',
                's.bias as source_bias',
                's.reliability as source_reliability',
                's.transparency as source_transparency',
                'COUNT(a.hash) AS articles_count',
                'MAX(a.crawled_at) AS source_crawled_at',
                'COUNT(CASE WHEN a.metadata IS NOT NULL THEN 1 ELSE NULL END) AS articles_metadata_available',
            )
            ->from('article', 'a')
            ->leftJoin('a', 'source', 's', 'a.source = s.name')
            ->groupBy('s.name')
            ->orderBy('articles_count', 'DESC')
            ->enableResultCache(new QueryCacheProfile(3600, SourceCacheKey::SOURCE_OVERVIEW_LIST->value))
        ;

        if ($query->userId instanceof UserId) {
            $qb->leftJoin('s', 'followed_source', 'f', 's.name = f.source AND f.follower_id = :userId')
                ->addSelect('CASE WHEN f.id IS NOT NULL THEN TRUE ELSE FALSE END as source_is_followed')
                ->setParameter('userId', $query->userId->toBinary(), ParameterType::BINARY)
                ->disableResultCache();
        }

        try {
            /** @var SlidingPaginationInterface<int, array<string, mixed>> $data */
            $data = $this->paginator->paginate($qb, $query->page->page, $query->page->limit);
        } catch (\Throwable $e) {
            throw NoResult::forQuery($qb->getSQL(), $qb->getParameters(), $e);
        }

        return new SourceOverviewList(
            items: array_map(
                fn ($item): SourceOverview => new SourceOverview(
                    name: Mapping::string($item, 'source_name'),
                    url: Mapping::string($item, 'source_url'),
                    articlesCount: Mapping::integer($item, 'articles_count'),
                    crawledAt: Mapping::string($item, 'source_crawled_at'),
                    displayName: Mapping::nullableString($item, 'source_display_name'),
                    updatedAt: Mapping::nullableString($item, 'updated_at'),
                    metadataAvailable: Mapping::integer($item, 'articles_metadata_available'),
                    followed: Mapping::boolean($item, 'source_is_followed'),
                    image: $this->assetUrlProvider->getUrl(
                        Mapping::string($item, 'source_name'),
                        AssetType::SOURCE_PROFILE_IMAGE
                    ),
                ),
                \iterator_to_array($data->getItems())
            ),
            pagination: Pagination::create($data->getPaginationData())
        );
    }
}
