<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\EventListener;

use App\Aggregator\Domain\Model\Entity\Article;
use App\Aggregator\Domain\Model\Entity\Source;
use App\Aggregator\Infrastructure\Persistence\Doctrine\CacheKey;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

/**
 * Class CacheInvalidationListener.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[AsDoctrineListener(Events::postRemove)]
#[AsDoctrineListener(Events::postUpdate)]
#[AsDoctrineListener(Events::postPersist)]
final readonly class CacheInvalidationListener
{
    private ?CacheItemPoolInterface $cache;

    public function __construct(Connection $connection)
    {
        $this->cache = $connection->getConfiguration()->getResultCache();
    }

    /**
     * @param LifecycleEventArgs<EntityManagerInterface> $event
     * @throws InvalidArgumentException
     */
    public function __invoke(LifecycleEventArgs $event): void
    {
        $entity = $event->getObject();

        if ($entity instanceof Article) {
            $this->cache?->deleteItems([
                CacheKey::ARTICLE_DETAILS->withId($entity->id->toString()),
                CacheKey::SOURCE_OVERVIEW->withId($entity->source->name),
                CacheKey::SOURCES_STATISTICS_OVERVIEW->withId($entity->source->name),
                CacheKey::SOURCES_STATISTICS_OVERVIEW->withId($entity->source->name),
                CacheKey::SOURCE_CATEGORIES_SHARES->withId($entity->source->name),
                CacheKey::SOURCE_PUBLICATION_GRAPH->withId($entity->source->name),
            ]);
        }

        if ($entity instanceof Source) {
            $this->cache?->deleteItems([
                CacheKey::SOURCE_OVERVIEW->withId($entity->name),
                CacheKey::SOURCES_STATISTICS_OVERVIEW->withId($entity->name),
            ]);
        }
    }
}
