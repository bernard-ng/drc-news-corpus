<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL;

use App\Aggregator\Application\UseCase\Query\GetLatestPublicationDateQuery;
use App\Aggregator\Application\UseCase\QueryHandler\GetLatestPublicationDateHandler;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

/**
 * Class GetLatestPublicationDateDBalHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetLatestPublicationDateDBalHandler implements GetLatestPublicationDateHandler
{
    public function __construct(
        private Connection $connection,
        private LoggerInterface $logger
    ) {
    }

    #[\Override]
    public function __invoke(GetLatestPublicationDateQuery $query): \DateTimeImmutable
    {
        try {
            $qb = $this->connection->createQueryBuilder();
            $qb
                ->from('article', 'a')
                ->select('MAX(a.published_at)')
                ->andWhere('a.source = :source');

            if ($query->category !== null) {
                $qb->andWhere('a.categories LIKE :category');
            }

            $statement = $this->connection->prepare($qb->getSQL());
            $statement->bindValue('source', $query->source);
            if ($query->category !== null) {
                $statement->bindValue('category', "%{$query->category}%");
            }

            /** @var string|false|null $result */
            $result = $statement->executeQuery()->fetchOne();
            if ($result === false || $result === null) {
                throw new \RuntimeException('Unable to fetch latest publication date');
            }

            return new \DateTimeImmutable($result);
        } catch (\Throwable $e) {
            $this->logger->critical($e->getMessage(), [
                'exception' => $e,
                'query' => $query,
            ]);

            throw new \RuntimeException('Unable to fetch latest publication date', previous: $e);
        }
    }
}
