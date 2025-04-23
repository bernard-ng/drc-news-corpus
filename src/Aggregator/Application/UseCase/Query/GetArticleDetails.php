<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\Query;

use App\Aggregator\Domain\Model\Entity\Identity\ArticleId;

/**
 * Class GetArticleDetails.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetArticleDetails
{
    public function __construct(
        public ArticleId $id
    ) {
    }
}
