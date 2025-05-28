<?php

declare(strict_types=1);

namespace App\FeedManagement\Presentation\Web\Controller;

use App\Aggregator\Domain\Model\Identity\ArticleId;
use App\FeedManagement\Application\UseCase\Command\RemoveArticleFromBookmark;
use App\FeedManagement\Domain\Model\Identity\BookmarkId;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

/**
 * Class RemoveArticleFromBookmarkController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class RemoveArticleFromBookmarkController extends AbstractController
{
    #[Route(
        path: '/api/feed/bookmarks/{bookmarkId}/articles/{articleId}',
        name: 'feed_management_remove_article_from_bookmark',
        requirements: [
            'bookmarkId' => Requirement::UUID_V7,
            'articleId' => Requirement::UUID_V7,
        ],
        methods: ['DELETE']
    )]
    public function __invoke(BookmarkId $bookmarkId, ArticleId $articleId): JsonResponse
    {
        $securityUser = $this->getSecurityUser();
        $this->handleCommand(new RemoveArticleFromBookmark($securityUser->userId, $articleId, $bookmarkId));

        return new JsonResponse(status: Response::HTTP_CREATED);
    }
}
