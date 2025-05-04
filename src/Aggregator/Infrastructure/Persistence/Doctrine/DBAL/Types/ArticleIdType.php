<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Persistence\Doctrine\DBAL\Types;

use App\Aggregator\Domain\Model\Identity\ArticleId;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

/**
 * Class ArticleId.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class ArticleIdType extends AbstractUidType
{
    #[\Override]
    public function getName(): string
    {
        return 'article_id';
    }

    #[\Override]
    protected function getUidClass(): string
    {
        return ArticleId::class;
    }
}
