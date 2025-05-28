<?php

declare(strict_types=1);

namespace App\FeedManagement\Presentation\Web\Controller;

use App\FeedManagement\Application\UseCase\Command\UpdateBookmark;
use App\FeedManagement\Domain\Model\Identity\BookmarkId;
use App\FeedManagement\Presentation\WriteModel\UpdateBookmarkModel;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

/**
 * Class UpdateBookmarkController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class UpdateBookmarkController extends AbstractController
{
    #[Route(
        path: '/api/feed/bookmarks/{bookmarkId}',
        name: 'feed_management_update_bookmark',
        requirements: [
            'bookmarkId' => Requirement::UUID_V7,
        ],
        methods: ['PUT']
    )]
    public function __invoke(BookmarkId $bookmarkId, #[MapRequestPayload] UpdateBookmarkModel $model): JsonResponse
    {
        $securityUser = $this->getSecurityUser();
        $this->handleCommand(new UpdateBookmark(
            $securityUser->userId,
            $bookmarkId,
            $model->name,
            $model->description,
            $model->isPublic
        ));

        return new JsonResponse(status: Response::HTTP_CREATED);
    }
}
