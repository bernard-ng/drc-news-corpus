<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL\Feature;

use App\Aggregator\Application\ReadModel\ArticleDetails;
use App\Aggregator\Application\ReadModel\ArticleList;
use App\Aggregator\Domain\Model\Entity\Identity\ArticleId;
use App\Aggregator\Domain\Model\ValueObject\Credibility\Bias;
use App\Aggregator\Domain\Model\ValueObject\Credibility\Credibility;
use App\Aggregator\Domain\Model\ValueObject\Credibility\Reliability;
use App\Aggregator\Domain\Model\ValueObject\Credibility\Transparency;
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
            ->addSelect('bias, transparency, reliability')
            ->from('article');
    }

    private function mapArticleDetails(array $data): ArticleDetails
    {
        return new ArticleDetails(
            ArticleId::fromBinary($data['id']),
            Mapping::string($data, 'title'),
            $this->createAbsoluteUri(
                Mapping::string($data, 'link'),
                Mapping::string($data, 'source')
            ),
            Mapping::string($data, 'categories'),
            Mapping::string($data, 'body'),
            Mapping::string($data, 'source'),
            Mapping::string($data, 'hash'),
            new Credibility(
                Mapping::enum($data, 'bias', Bias::class),
                Mapping::enum($data, 'reliability', Reliability::class),
                Mapping::enum($data, 'transparency', Transparency::class)
            ),
            Mapping::datetime($data, 'published_at'),
            Mapping::datetime($data, 'crawled_at'),
            Mapping::nullableDatetime($data, 'updated_at')
        );
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

    private function createAbsoluteUri(string $link, string $source): string
    {
        if (str_starts_with($link, 'http')) {
            return $link;
        }

        return sprintf('https://%s/%s', $source, trim($link, '/'));
    }
}
