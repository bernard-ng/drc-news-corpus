<?php

declare(strict_types=1);

namespace App\FeedManagement\Presentation\Web\Controller\Source;

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
        path: '/api/feed/sources/{source}/unfollow',
        name: 'feed_management_unfollow_source',
        requirements: [
            'source' => Requirement::CATCH_ALL,
        ],
        methods: ['DELETE']
    )]
    public function __invoke(string $source): JsonResponse
    {
        $securityUser = $this->getSecurityUser();

        $this->handleCommand(new UnfollowSource($source, $securityUser->userId));

        return new JsonResponse(status: Response::HTTP_OK);
    }
}
