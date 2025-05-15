<?php

declare(strict_types=1);

namespace App\FeedManagement\Infrastructure\Persistence\Doctrine\ORM;

use App\FeedManagement\Domain\Exception\BookmarkNotFound;
use App\FeedManagement\Domain\Model\Entity\Bookmark;
use App\FeedManagement\Domain\Model\Identity\BookmarkId;
use App\FeedManagement\Domain\Model\Repository\BookmarkRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class BookmarkOrmRepository.
 *
 * @extends ServiceEntityRepository<Bookmark>
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class BookmarkOrmRepository extends ServiceEntityRepository implements BookmarkRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bookmark::class);
    }

    public function add(Bookmark $bookmark): void
    {
        $this->getEntityManager()->persist($bookmark);
        $this->getEntityManager()->flush();
    }

    public function remove(Bookmark $bookmark): void
    {
        $this->getEntityManager()->remove($bookmark);
        $this->getEntityManager()->flush();
    }

    public function getById(BookmarkId $bookmarkId): Bookmark
    {
        $bookmark = $this->findOneBy([
            'id' => $bookmarkId,
        ]);

        if ($bookmark === null) {
            throw BookmarkNotFound::withId($bookmarkId);
        }

        return $bookmark;
    }
}
