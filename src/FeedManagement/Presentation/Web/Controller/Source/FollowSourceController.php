<?php

declare(strict_types=1);

namespace App\FeedManagement\Presentation\Web\Controller\Source;

use App\FeedManagement\Application\UseCase\Command\FollowSource;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

/**
 * Class FollowSourceController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class FollowSourceController extends AbstractController
{
    #[Route(
        path: '/api/feed/sources/{source}/follow',
        name: 'feed_management_follow_source',
        requirements: [
            'source' => Requirement::CATCH_ALL,
        ],
        methods: ['POST']
    )]
    public function __invoke(string $source): JsonResponse
    {
        $securityUser = $this->getSecurityUser();

        $this->handleCommand(new FollowSource($source, $securityUser->userId));

        return new JsonResponse(status: Response::HTTP_CREATED);
    }
}
