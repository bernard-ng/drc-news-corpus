<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\QueryHandler;

use App\Aggregator\Application\ReadModel\ArticleForExport;
use App\Aggregator\Application\UseCase\Query\GetArticlesForExport;
use App\SharedKernel\Application\Messaging\QueryHandler;

/**
 * Class GetArticlesForExportHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface GetArticlesForExportHandler extends QueryHandler
{
    /**
     * @return iterable<ArticleForExport>
     */
    public function __invoke(GetArticlesForExport $query): iterable;
}
