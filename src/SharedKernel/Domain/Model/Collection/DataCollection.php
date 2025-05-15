<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Model\Collection;

use Doctrine\Common\Collections\ArrayCollection as DoctrineArrayCollection;

/**
 * Class DataCollection.
 *
 * @phpstan-template TKey of array-key
 * @phpstan-template T
 * @phpstan-extends DoctrineArrayCollection<int, T>
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class DataCollection extends DoctrineArrayCollection
{
}
