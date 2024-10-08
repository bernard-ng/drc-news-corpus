<?php

declare(strict_types=1);

namespace App\Aggregator\Application\UseCase\QueryHandler;

use App\Aggregator\Application\UseCase\Query\GetLastCrawlDateQuery;
use App\Aggregator\Domain\Repository\ArticleRepository;
use App\SharedKernel\Application\Bus\QueryHandler;

/**
 * Class GetLastCrawlDateHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetLastCrawlDateHandler implements QueryHandler
{
    public function __construct(
        private ArticleRepository $articleRepository
    ) {
    }

    public function __invoke(GetLastCrawlDateQuery $query): string
    {
        return $this->articleRepository->getLastCrawlDate($query->source, $query->category);
    }
}
