<?php

declare(strict_types=1);

namespace App\Aggregator\Presentation\Web\Controller;

use App\Aggregator\Application\ReadModel\Statistics\SourceStatisticsDetails;
use App\Aggregator\Application\UseCase\Query\GetSourceStatisticsDetails;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

/**
 * Class GetSourceStatisticsDetailsController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[AsController]
final class GetSourceStatisticsDetailsController extends AbstractController
{
    #[Route(
        path: '/api/aggregator/statistics/{source}',
        name: 'aggregator_source_statistics_details',
        requirements: [
            'source' => Requirement::ASCII_SLUG,
        ],
        methods: ['GET']
    )]
    public function __invoke(string $source): JsonResponse
    {
        /** @var SourceStatisticsDetails $stats */
        $stats = $this->handleQuery(new GetSourceStatisticsDetails($source));

        return JsonResponse::fromJsonString($this->serialize($stats));
    }
}
