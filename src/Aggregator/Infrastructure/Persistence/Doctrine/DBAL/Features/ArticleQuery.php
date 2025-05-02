<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL\Features;

use App\Aggregator\Application\ReadModel\ArticleDetails;
use App\Aggregator\Application\ReadModel\ArticleList;
use App\Aggregator\Domain\Model\Entity\Identity\ArticleId;
use App\Aggregator\Domain\Model\ValueObject\Crawling\OpenGraph;
use App\Aggregator\Domain\Model\ValueObject\Link;
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
            ->addSelect('bias, transparency, reliability, sentiment, metadata')
            ->from('article');
    }

    /**
     * @param SlidingPaginationInterface<int, array<string, mixed>> $data
     */
    private function mapArticleList(SlidingPaginationInterface $data): ArticleList
    {
        return new ArticleList(
            items: array_map(
                fn ($item) => $this->mapArticleDetails($item),
                \iterator_to_array($data->getItems())
            ),
            pagination: Pagination::create($data->getPaginationData())
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
            Mapping::datetime($data, 'published_at'),
            Mapping::datetime($data, 'crawled_at'),
            Mapping::nullableDatetime($data, 'updated_at')
        );
    }
}
