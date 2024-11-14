<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\QueryHandler;

use App\Aggregator\Application\UseCase\Query\GetLatestPublicationDateQuery;
use App\SharedKernel\Application\Bus\QueryHandler;

/**
 * Interface GetLatestPublicationDateHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface GetLatestPublicationDateHandler extends QueryHandler
{
    public function __invoke(GetLatestPublicationDateQuery $query): \DateTimeImmutable;
}
