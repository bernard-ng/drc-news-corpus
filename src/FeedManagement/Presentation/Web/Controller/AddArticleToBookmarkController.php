<?php

declare(strict_types=1);

namespace App\FeedManagement\Presentation\Web\Controller;

use App\Aggregator\Domain\Model\Identity\ArticleId;
use App\FeedManagement\Application\UseCase\Command\AddArticleToBookmark;
use App\FeedManagement\Domain\Model\Identity\BookmarkId;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

/**
 * Class AddArticleToBookmarkController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class AddArticleToBookmarkController extends AbstractController
{
    #[Route(
        path: '/api/feed/bookmarks/{bookmarkId}/articles/{articleId}',
        name: 'feed_management_add_article_to_bookmark',
        requirements: [
            'bookmarkId' => Requirement::UUID_V7,
            'articleId' => Requirement::UUID_V7,
        ],
        methods: ['POST']
    )]
    public function __invoke(BookmarkId $bookmarkId, ArticleId $articleId): JsonResponse
    {
        $securityUser = $this->getSecurityUser();
        $this->handleCommand(new AddArticleToBookmark($securityUser->userId, $articleId, $bookmarkId));

        return new JsonResponse(status: Response::HTTP_CREATED);
    }
}
