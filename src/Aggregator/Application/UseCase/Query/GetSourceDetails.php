<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\Query;

use App\IdentityAndAccess\Domain\Model\Identity\UserId;

/**
 * Class GetSourceDetails.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetSourceDetails
{
    public function __construct(
        public string $source,
        public UserId $userId,
    ) {
    }
}
