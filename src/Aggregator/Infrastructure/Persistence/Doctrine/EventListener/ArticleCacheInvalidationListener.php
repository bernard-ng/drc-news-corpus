<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\EventListener;

use App\Aggregator\Domain\Model\Entity\Article;
use App\Aggregator\Infrastructure\Persistence\Doctrine\CacheKey\ArticleCacheKey;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;

/**
 * Class ArticleCacheInvalidationListener.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[AsEntityListener(event: Events::postRemove, entity: Article::class)]
#[AsEntityListener(event: Events::postUpdate, entity: Article::class)]
#[AsEntityListener(event: Events::postPersist, entity: Article::class)]
final readonly class ArticleCacheInvalidationListener
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
    public function __invoke(Article $entity, LifecycleEventArgs $event): void
    {
        try {
            $this->cache?->deleteItems([
                ArticleCacheKey::ARTICLE_DETAILS->withId($entity->id->toString()),
            ]);
        } catch (\Throwable $e) {
            $this->logger->emergency('Failed to invalidate article cache', [
                'event' => $event::class,
                'exception' => $e,
                'article_id' => $entity->id->toString(),
            ]);
        }
    }
}
