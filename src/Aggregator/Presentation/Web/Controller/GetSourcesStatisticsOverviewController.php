<?php

declare(strict_types=1);

namespace App\Aggregator\Presentation\Web\Controller;

use App\Aggregator\Application\ReadModel\Statistics\SourcesStatisticsOverview;
use App\Aggregator\Application\UseCase\Query\GetSourcesStatisticsOverview;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class GetSourcesStatisticsOverviewController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[AsController]
final class GetSourcesStatisticsOverviewController extends AbstractController
{
    #[Route(
        path: '/api/aggregator/statistics',
        name: 'aggregator_source_statistics_overview',
        methods: ['GET']
    )]
    public function __invoke(): JsonResponse
    {
        /** @var SourcesStatisticsOverview $stats */
        $stats = $this->handleQuery(new GetSourcesStatisticsOverview());

        return JsonResponse::fromJsonString($this->serialize($stats));
    }
}
