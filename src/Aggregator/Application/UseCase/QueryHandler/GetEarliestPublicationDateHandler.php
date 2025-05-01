<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\QueryHandler;

use App\Aggregator\Application\UseCase\Query\GetEarliestPublicationDate;
use App\SharedKernel\Application\Messaging\QueryHandler;

/**
 * Interface GetEarliestPublicationDateHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface GetEarliestPublicationDateHandler extends QueryHandler
{
    public function __invoke(GetEarliestPublicationDate $query): \DateTimeImmutable;
}
