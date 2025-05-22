<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL;

use App\Aggregator\Application\ReadModel\ArticleDetails;
use App\Aggregator\Application\ReadModel\Source\SourceReference;
use App\Aggregator\Application\UseCase\Query\GetArticleDetails;
use App\Aggregator\Application\UseCase\QueryHandler\GetArticleDetailsHandler;
use App\Aggregator\Domain\Exception\ArticleNotFound;
use App\Aggregator\Domain\Model\Identity\ArticleId;
use App\Aggregator\Domain\Model\ValueObject\Crawling\OpenGraph;
use App\Aggregator\Domain\Model\ValueObject\Link;
use App\Aggregator\Domain\Model\ValueObject\ReadingTime;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Bias;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Credibility;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Reliability;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Sentiment;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Transparency;
use App\SharedKernel\Application\Asset\AssetType;
use App\SharedKernel\Application\Asset\AssetUrlProvider;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\Mapping;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\NoResult;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;

/**
 * Class GetArticleDetailsDbalHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetArticleDetailsDbalHandler implements GetArticleDetailsHandler
{
    public function __construct(
        private Connection $connection,
        private AssetUrlProvider $assetUrlProvider,
    ) {
    }

    #[\Override]
    public function __invoke(GetArticleDetails $query): ArticleDetails
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'a.id as article_id',
                'a.title as article_title',
                'a.link as article_link',
                'a.categories as article_categories',
                'a.body as article_body',
                'a.hash as article_hash',
                'a.published_at as article_published_at',
                'a.crawled_at as article_crawled_at',
                'a.updated_at as article_updated_at',
                'a.bias as article_bias',
                'a.reliability as article_reliability',
                'a.transparency as article_transparency',
                'a.sentiment as article_sentiment',
                'a.metadata as article_metadata',
                'a.reading_time as article_reading_time',
            )
            ->addSelect(
                's.display_name as source_display_name',
                's.url as source_url',
                's.name as source_name'
            )
            ->addSelect('CASE WHEN b.id IS NOT NULL THEN TRUE ELSE FALSE END as article_is_bookmarked')
            ->leftJoin('a', 'source', 's', 'a.source = s.name')
            ->leftJoin('a', 'bookmark_article', 'ba', 'a.id = ba.article_id')
            ->leftJoin('ba', 'bookmark', 'b', 'ba.bookmark_id = b.id AND b.user_id = :userId')
            ->from('article', 'a')
            ->where('a.id = :articleId')
            ->setParameter('articleId', $query->id->toBinary(), ParameterType::BINARY)
            ->setParameter('userId', $query->userId->toBinary(), ParameterType::BINARY)
            //->enableResultCache(new QueryCacheProfile(0, ArticleCacheKey::ARTICLE_DETAILS->withId($query->id->toString())))
        ;

        try {
            /** @var array<string, mixed>|false $data */
            $data = $qb->executeQuery()->fetchAssociative();
        } catch (\Throwable $e) {
            throw NoResult::forQuery($qb->getSQL(), $qb->getParameters(), $e);
        }

        if ($data === false) {
            throw ArticleNotFound::withId($query->id);
        }

        return $this->mapArticleDetails($data);
    }

    private function mapArticleDetails(array $item): ArticleDetails
    {
        return new ArticleDetails(
            ArticleId::fromBinary($item['article_id']),
            Mapping::string($item, 'article_title'),
            Link::from(Mapping::string($item, 'article_link')),
            explode(',', Mapping::string($item, 'article_categories')),
            Mapping::string($item, 'article_body'),
            new SourceReference(
                Mapping::string($item, 'source_name'),
                Mapping::nullableString($item, 'source_display_name'),
                $this->assetUrlProvider->getUrl(
                    Mapping::string($item, 'source_name'),
                    AssetType::SOURCE_PROFILE_IMAGE
                ),
                Mapping::string($item, 'source_url'),
            ),
            Mapping::string($item, 'article_hash'),
            new Credibility(
                Mapping::enum($item, 'article_bias', Bias::class),
                Mapping::enum($item, 'article_reliability', Reliability::class),
                Mapping::enum($item, 'article_transparency', Transparency::class)
            ),
            Mapping::enum($item, 'article_sentiment', Sentiment::class),
            OpenGraph::tryFrom(Mapping::nullableString($item, 'article_metadata')),
            ReadingTime::create(Mapping::nullableInteger($item, 'article_reading_time')),
            Mapping::datetime($item, 'article_published_at'),
            Mapping::datetime($item, 'article_crawled_at'),
            Mapping::nullableDatetime($item, 'article_updated_at'),
            Mapping::boolean($item, 'article_is_bookmarked'),
        );
    }
}
