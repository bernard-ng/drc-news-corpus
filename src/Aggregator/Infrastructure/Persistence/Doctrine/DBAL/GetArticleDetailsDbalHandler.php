<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL;

use App\Aggregator\Application\ReadModel\ArticleDetails;
use App\Aggregator\Application\UseCase\Query\GetArticleDetails;
use App\Aggregator\Application\UseCase\QueryHandler\GetArticleDetailsHandler;
use App\Aggregator\Domain\Exception\ArticleNotFound;
use App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL\Feature\ArticleQuery;
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
    use ArticleQuery;

    public function __construct(
        private Connection $connection
    ) {
    }

    #[\Override]
    public function __invoke(GetArticleDetails $query): ArticleDetails
    {
        $qb = $this->createArticleBaseQuery()
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

        return $this->mapArticleDetails($data);
    }
}
