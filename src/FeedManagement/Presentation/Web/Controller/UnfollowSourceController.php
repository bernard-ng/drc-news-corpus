<?php

declare(strict_types=1);

namespace App\FeedManagement\Presentation\Web\Controller;

use App\Aggregator\Domain\Model\Identity\SourceId;
use App\FeedManagement\Application\UseCase\Command\UnfollowSource;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

/**
 * Class UnfollowSourceController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class UnfollowSourceController extends AbstractController
{
    #[Route(
        path: '/api/feed/sources/{sourceId}/unfollow',
        name: 'feed_management_unfollow_source',
        requirements: [
            'sourceId' => Requirement::UUID_V7,
        ],
        methods: ['DELETE']
    )]
    public function __invoke(SourceId $sourceId): JsonResponse
    {
        $securityUser = $this->getSecurityUser();
        $this->handleCommand(new UnfollowSource($sourceId, $securityUser->userId));

        return new JsonResponse(status: Response::HTTP_OK);
    }
}
