<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL;

use App\Aggregator\Application\ReadModel\ArticleDetails;
use App\Aggregator\Application\UseCase\Query\GetArticleDetails;
use App\Aggregator\Application\UseCase\QueryHandler\GetArticleDetailsHandler;
use App\Aggregator\Domain\Exception\ArticleNotFound;
use App\Aggregator\Domain\Model\Entity\Identity\ArticleId;
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
        private Connection $connection
    ) {
    }

    #[\Override]
    public function __invoke(GetArticleDetails $query): ArticleDetails
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('id, title, link, categories, body, source, hash, published_at, crawled_at')
            ->from('article')
            ->where('id = :id')
            ->setParameter('id', $query->id->toBinary(), ParameterType::BINARY);

        try {
            /** @var array<string, mixed>|false $data */
            $data = $qb->executeQuery()->fetchAssociative();
        } catch (\Throwable $e) {
            throw NoResult::forQuery($qb->getSQL(), $qb->getParameters(), $e);
        }

        if ($data === false) {
            throw ArticleNotFound::withId($query->id);
        }

        return $this->mapArticle($data);
    }

    private function mapArticle(array $data): ArticleDetails
    {
        return new ArticleDetails(
            ArticleId::fromBinary($data['id']),
            Mapping::string($data, 'title'),
            Mapping::string($data, 'link'),
            Mapping::string($data, 'categories'),
            Mapping::string($data, 'body'),
            Mapping::string($data, 'source'),
            Mapping::string($data, 'hash'),
            Mapping::datetime($data, 'published_at'),
            Mapping::datetime($data, 'crawled_at')
        );
    }
}
