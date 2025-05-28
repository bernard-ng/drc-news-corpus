<?php

declare(strict_types=1);

namespace App\FeedManagement\Presentation\Web\Controller;

use App\FeedManagement\Application\UseCase\Command\DeleteBookmark;
use App\FeedManagement\Domain\Model\Identity\BookmarkId;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

/**
 * Class DeleteBookmarkController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class DeleteBookmarkController extends AbstractController
{
    #[Route(
        path: '/api/feed/bookmarks/{bookmarkId}',
        name: 'feed_management_delete_bookmark',
        requirements: [
            'bookmarkId' => Requirement::UUID_V7,
        ],
        methods: ['DELETE']
    )]
    public function __invoke(BookmarkId $bookmarkId): JsonResponse
    {
        $securityUser = $this->getSecurityUser();
        $this->handleCommand(new DeleteBookmark($securityUser->userId, $bookmarkId));

        return new JsonResponse(status: Response::HTTP_CREATED);
    }
}
