<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\QueryHandler;

use App\Aggregator\Application\ReadModel\ExportedArticle;
use App\Aggregator\Application\UseCase\Query\ExportQuery;
use App\SharedKernel\Application\Bus\QueryHandler;

/**
 * Class ExportHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface ExportHandler extends QueryHandler
{
    /**
     * @return iterable<ExportedArticle>
     */
    public function __invoke(ExportQuery $query): iterable;
}
