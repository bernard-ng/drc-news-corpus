<?php

declare(strict_types=1);

namespace App\FeedManagement\Presentation\Web\Controller\Bookmark;

use App\FeedManagement\Application\ReadModel\BookmarkInfosList;
use App\FeedManagement\Application\UseCase\Query\GetBookmarkInfosList;
use App\SharedKernel\Domain\Model\ValueObject\Page;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class GetBookmarkedArticleListController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[AsController]
final class GetBookmarkInfosListController extends AbstractController
{
    #[Route(
        path: '/api/feed/bookmarks',
        name: 'feed_management_bookmark_infos_list',
        methods: ['GET']
    )]
    public function __invoke(#[MapQueryString] Page $page): JsonResponse
    {
        $securityUser = $this->getSecurityUser();

        /** @var BookmarkInfosList $data */
        $data = $this->handleQuery(new GetBookmarkInfosList($securityUser->userId, $page));

        return JsonResponse::fromJsonString($this->serialize($data));
    }
}
