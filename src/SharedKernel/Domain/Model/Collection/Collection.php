<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Model\Collection;

use Doctrine\Common\Collections\Collection as DoctrineCollection;

/**
 * Interface Collection.
 *
 * @phpstan-template TKey of array-key
 * @phpstan-template T
 * @phpstan-extends DoctrineCollection<TKey, T>
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface Collection extends DoctrineCollection
{
}
