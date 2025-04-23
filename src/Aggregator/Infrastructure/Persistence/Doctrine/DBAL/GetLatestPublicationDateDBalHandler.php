<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL;

use App\Aggregator\Application\UseCase\Query\GetLatestPublicationDate;
use App\Aggregator\Application\UseCase\QueryHandler\GetLatestPublicationDateHandler;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\NoResult;
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
    public function __invoke(GetLatestPublicationDate $query): \DateTimeImmutable
    {
        $qb = $this->connection->createQueryBuilder()
            ->from('article', 'a')
            ->select('MAX(a.published_at)')
            ->andWhere('a.source = :source')
            ->setParameter('source', $query->source);

        if ($query->category !== null) {
            $qb->andWhere('a.categories LIKE :category')
                ->setParameter('category', "%{$query->category}%");
        }

        try {
            /** @var string|null $date */
            $date = $qb->executeQuery()->fetchOne();

            return new \DateTimeImmutable($date ?? 'now');
        } catch (\Throwable $e) {
            $this->logger->critical('Unable to fetch latest publication date');
            throw NoResult::forQuery($qb->getSQL(), $qb->getParameters(), $e);
        }
    }
}
