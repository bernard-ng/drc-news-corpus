<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\QueryHandler;

use App\Aggregator\Application\UseCase\Query\GetLatestPublicationDate;
use App\SharedKernel\Application\Messaging\QueryHandler;

/**
 * Interface GetLatestPublicationDateHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface GetLatestPublicationDateHandler extends QueryHandler
{
    public function __invoke(GetLatestPublicationDate $query): \DateTimeImmutable;
}
