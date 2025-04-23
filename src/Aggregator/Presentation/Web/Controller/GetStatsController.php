<?php

declare(strict_types=1);

namespace App\Aggregator\Presentation\Web\Controller;

use App\Aggregator\Application\ReadModel\Statistics;
use App\Aggregator\Application\UseCase\Query\GetStatistics;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class GetStatsController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[AsController]
final class GetStatsController extends AbstractController
{
    #[Route('/api/aggregator/statistics', name: 'aggregator_statistics', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        /** @var Statistics $stats */
        $stats = $this->handleQuery(new GetStatistics());

        return JsonResponse::fromJsonString($this->serialize($stats));
    }
}
