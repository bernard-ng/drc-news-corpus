<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\Query;

use Symfony\Component\Uid\Uuid;

/**
 * Class GetArticleDetailsQuery.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetArticleDetailsQuery
{
    public function __construct(
        public Uuid $id
    ) {
    }
}
