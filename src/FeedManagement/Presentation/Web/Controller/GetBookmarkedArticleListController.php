<?php

declare(strict_types=1);

namespace App\FeedManagement\Presentation\Web\Controller;

use App\FeedManagement\Application\UseCase\Query\GetBookmarkedArticleList;
use App\FeedManagement\Domain\Model\Identity\BookmarkId;
use App\SharedKernel\Domain\Model\Pagination\Page;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

/**
 * Class GetBookmarkedArticleListController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class GetBookmarkedArticleListController extends AbstractController
{
    #[Route(
        path: '/api/feed/bookmarks/{bookmarkId}/articles',
        name: 'feed_management_bookmarked_article_list',
        requirements: [
            'bookmarkId' => Requirement::UUID_V7,
        ],
        methods: ['GET']
    )]
    public function __invoke(BookmarkId $bookmarkId, #[MapQueryString] Page $page): JsonResponse
    {
        $securityUser = $this->getSecurityUser();
        $data = $this->handleQuery(new GetBookmarkedArticleList($securityUser->userId, $bookmarkId, $page));

        return JsonResponse::fromJsonString($this->serialize($data));
    }
}
