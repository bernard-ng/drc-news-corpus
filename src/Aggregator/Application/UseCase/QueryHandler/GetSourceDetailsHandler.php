<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\QueryHandler;

use App\Aggregator\Application\ReadModel\Source\SourceDetails;
use App\Aggregator\Application\UseCase\Query\GetSourceDetails;
use App\SharedKernel\Application\Messaging\QueryHandler;

/**
 * Interface GetSourceDetailsHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface GetSourceDetailsHandler extends QueryHandler
{
    public function __invoke(GetSourceDetails $query): SourceDetails;
}
