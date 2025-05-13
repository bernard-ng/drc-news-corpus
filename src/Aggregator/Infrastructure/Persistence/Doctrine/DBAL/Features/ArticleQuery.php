<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL\Features;

use App\Aggregator\Application\ReadModel\ArticleDetails;
use App\Aggregator\Application\ReadModel\ArticleOverview;
use App\Aggregator\Application\ReadModel\ArticleOverviewList;
use App\Aggregator\Domain\Model\Identity\ArticleId;
use App\Aggregator\Domain\Model\ValueObject\Crawling\OpenGraph;
use App\Aggregator\Domain\Model\ValueObject\Link;
use App\Aggregator\Domain\Model\ValueObject\ReadingTime;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Bias;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Credibility;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Reliability;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Sentiment;
use App\Aggregator\Domain\Model\ValueObject\Scoring\Transparency;
use App\SharedKernel\Domain\Model\ValueObject\Pagination;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\Mapping;
use Doctrine\DBAL\Query\QueryBuilder;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPaginationInterface;

/**
 * Enum ArticleQuery.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
trait ArticleQuery
{
    private function createArticleBaseQuery(): QueryBuilder
    {
        return $this->connection->createQueryBuilder()
            ->select('id, title, link, categories, body, source, hash, published_at, crawled_at, updated_at')
            ->addSelect('bias, transparency, reliability, sentiment, metadata, reading_time')
            ->from('article');
    }

    private function createArticleOverviewBaseQuery(): QueryBuilder
    {
        return $this->connection->createQueryBuilder()
            ->select('id, title, link, categories, LEFT(body, 200) as excerpt')
            ->addSelect('metadata, source, published_at, reading_time')
            ->from('article');
    }

    /**
     * @param SlidingPaginationInterface<int, array<string, mixed>> $data
     */
    private function mapArticleOverviewList(SlidingPaginationInterface $data): ArticleOverviewList
    {
        return new ArticleOverviewList(
            items: array_map(
                fn ($item) => $this->mapArticleOverview($item),
                \iterator_to_array($data->getItems())
            ),
            pagination: Pagination::create($data->getPaginationData())
        );
    }

    private function mapArticleOverview(array $data): ArticleOverview
    {
        $openGraph = OpenGraph::tryFrom(Mapping::nullableString($data, 'metadata'));

        return new ArticleOverview(
            ArticleId::fromBinary($data['id']),
            Mapping::string($data, 'title'),
            Link::from(Mapping::string($data, 'link')),
            explode(',', Mapping::string($data, 'categories')),
            trim(Mapping::string($data, 'excerpt')),
            Mapping::string($data, 'source'),
            $openGraph?->image,
            ReadingTime::create(Mapping::nullableInteger($data, 'reading_time')),
            Mapping::datetime($data, 'published_at')
        );
    }

    private function mapArticleDetails(array $data): ArticleDetails
    {
        return new ArticleDetails(
            ArticleId::fromBinary($data['id']),
            Mapping::string($data, 'title'),
            Link::from(Mapping::string($data, 'link'), Mapping::string($data, 'source')),
            explode(',', Mapping::string($data, 'categories')),
            Mapping::string($data, 'body'),
            Mapping::string($data, 'source'),
            Mapping::string($data, 'hash'),
            new Credibility(
                Mapping::enum($data, 'bias', Bias::class),
                Mapping::enum($data, 'reliability', Reliability::class),
                Mapping::enum($data, 'transparency', Transparency::class)
            ),
            Mapping::enum($data, 'sentiment', Sentiment::class),
            OpenGraph::tryFrom(Mapping::nullableString($data, 'metadata')),
            ReadingTime::create(Mapping::nullableInteger($data, 'reading_time')),
            Mapping::datetime($data, 'published_at'),
            Mapping::datetime($data, 'crawled_at'),
            Mapping::nullableDatetime($data, 'updated_at')
        );
    }
}
