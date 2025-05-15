<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\EventListener;

use App\Aggregator\Domain\Model\Entity\Source;
use App\Aggregator\Infrastructure\Persistence\Doctrine\CacheKey;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;

/**
 * Class SourceCacheInvalidationListener.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[AsEntityListener(event: Events::postRemove, entity: Source::class)]
#[AsEntityListener(event: Events::postUpdate, entity: Source::class)]
#[AsEntityListener(event: Events::postPersist, entity: Source::class)]
final readonly class SourceCacheInvalidationListener
{
    private ?CacheItemPoolInterface $cache;

    public function __construct(
        Connection $connection,
        private LoggerInterface $logger
    ) {
        $this->cache = $connection->getConfiguration()->getResultCache();
    }

    /**
     * @param LifecycleEventArgs<EntityManagerInterface> $event
     */
    public function __invoke(Source $entity, LifecycleEventArgs $event): void
    {
        try {
            $this->cache?->deleteItems([
                CacheKey::SOURCE_OVERVIEW->withId($entity->name),
                CacheKey::SOURCES_STATISTICS_OVERVIEW->withId($entity->name),
            ]);
        } catch (\Throwable $e) {
            $this->logger->emergency('Failed to invalidate source cache', [
                'event' => $event::class,
                'exception' => $e,
                'source_id' => $entity->name,
            ]);
        }
    }
}
