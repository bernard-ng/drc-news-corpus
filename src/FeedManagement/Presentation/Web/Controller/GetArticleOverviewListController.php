<?php

declare(strict_types=1);

namespace App\FeedManagement\Presentation\Web\Controller;

use App\FeedManagement\Application\UseCase\Query\GetArticleOverviewList;
use App\FeedManagement\Domain\Model\Filters\ArticleFilters;
use App\SharedKernel\Domain\Model\Pagination\Page;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class GetArticleOverviewListController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class GetArticleOverviewListController extends AbstractController
{
    #[Route(
        path: 'api/feed/articles',
        name: 'feed_management_article_overview_list',
        methods: ['GET']
    )]
    public function __invoke(
        #[MapQueryString] Page $page,
        #[MapQueryString] ArticleFilters $filters
    ): JsonResponse {
        $securityUser = $this->getSecurityUser();
        $data = $this->handleQuery(new GetArticleOverviewList($securityUser->userId, $page, $filters));

        return JsonResponse::fromJsonString($this->serialize($data));
    }
}
