<?php

declare(strict_types=1);

namespace App\FeedManagement\Presentation\Web\Controller;

use App\Aggregator\Domain\Model\Identity\SourceId;
use App\FeedManagement\Application\UseCase\Query\GetSourceDetails;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

/**
 * Class GetSourceDetailsController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class GetSourceDetailsController extends AbstractController
{
    #[Route(
        path: '/api/feed/sources/{sourceId}',
        name: 'feed_management_source_details',
        requirements: [
            'sourceId' => Requirement::UUID_V7,
        ],
        methods: ['GET']
    )]
    public function __invoke(SourceId $sourceId): JsonResponse
    {
        $securityUser = $this->getSecurityUser();
        $data = $this->handleQuery(new GetSourceDetails($sourceId, $securityUser->userId));

        return JsonResponse::fromJsonString($this->serialize($data));
    }
}
