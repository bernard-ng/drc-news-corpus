<?php

declare(strict_types=1);

namespace App\Aggregator\Presentation\Web\Controller;

use App\Aggregator\Application\ReadModel\Source\SourceDetails;
use App\Aggregator\Application\UseCase\Query\GetSourceDetails;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

/**
 * Class GetSourceDetailsController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[AsController]
final class GetSourceDetailsController extends AbstractController
{
    #[Route(
        path: '/api/aggregator/sources/{source}',
        name: 'aggregator_source_details',
        requirements: [
            'source' => Requirement::CATCH_ALL,
        ],
        methods: ['GET']
    )]
    public function __invoke(string $source): JsonResponse
    {
        $securityUser = $this->getSecurityUser();

        /** @var SourceDetails $stats */
        $stats = $this->handleQuery(new GetSourceDetails($source, $securityUser->userId));

        return JsonResponse::fromJsonString($this->serialize($stats));
    }
}
