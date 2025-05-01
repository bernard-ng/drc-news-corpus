<?php

declare(strict_types=1);

namespace App\Aggregator\Presentation\Web\Controller;

use App\Aggregator\Application\ReadModel\Statistics\SourceStatisticsDetails;
use App\Aggregator\Application\UseCase\Query\GetSourceStatisticsDetails;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class GetSourceStatisticsDetailsController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[AsController]
#[Route('/api/aggregator/statistics/{source}', name: 'aggregator_statistics_details', methods: ['GET'])]
final class GetSourceStatisticsDetailsController extends AbstractController
{
    public function __invoke(string $source): JsonResponse
    {
        /** @var SourceStatisticsDetails $stats */
        $stats = $this->handleQuery(new GetSourceStatisticsDetails($source));

        return JsonResponse::fromJsonString($this->serialize($stats));
    }
}
