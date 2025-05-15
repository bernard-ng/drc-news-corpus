<?php

declare(strict_types=1);

namespace App\FeedManagement\Infrastructure\Persistence\Doctrine\EventListener;

use App\FeedManagement\Domain\Model\Entity\Bookmark;
use App\FeedManagement\Infrastructure\Persistence\Doctrine\CacheKey\BookmarkCacheKey;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BookmarkCacheInvalidationListener.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[AsEntityListener(event: Events::postPersist, entity: Bookmark::class)]
#[AsEntityListener(event: Events::postUpdate, entity: Bookmark::class)]
#[AsEntityListener(event: Events::postRemove, entity: Bookmark::class)]
final readonly class BookmarkCacheInvalidationListener
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
    public function __invoke(Bookmark $entity, LifecycleEventArgs $event): void
    {
        try {
            $this->cache?->deleteItems([
                BookmarkCacheKey::BOOKMARK_INFO->withId($entity->id->toString()),
                BookmarkCacheKey::BOOKMARKED_ARTICLE_LIST->withId($entity->id->toString()),
                BookmarkCacheKey::BOOKMARK_INFO_LIST->withId($entity->user->id->toString()),
            ]);
        } catch (\Throwable $e) {
            $this->logger->emergency('Failed to invalidate bookmark cache', [
                'event' => $event::class,
                'exception' => $e,
                'bookmark_id' => $entity->id->toString(),
            ]);
        }
    }
}
