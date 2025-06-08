<?php

declare(strict_types=1);

namespace App\FeedManagement\Presentation\Web\Controller;

use App\Aggregator\Domain\Model\Identity\SourceId;
use App\FeedManagement\Application\UseCase\Query\GetSourceArticleOverviewList;
use App\FeedManagement\Domain\Model\Filters\ArticleFilters;
use App\SharedKernel\Domain\Model\Pagination\Page;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

/**
 * Class GetSourceArticleOverviewListController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class GetSourceArticleOverviewListController extends AbstractController
{
    #[Route(
        path: 'api/feed/sources/{sourceId}/articles',
        name: 'feed_management_source_article_list',
        requirements: [
            'sourceId' => Requirement::UUID_V7,
        ],
        methods: ['GET']
    )]
    public function __invoke(
        SourceId $sourceId,
        #[MapQueryString] Page $page,
        #[MapQueryString] ArticleFilters $filters
    ): JsonResponse {
        $securityUser = $this->getSecurityUser();
        $data = $this->handleQuery(new GetSourceArticleOverviewList($sourceId, $securityUser->userId, $page, $filters));

        return JsonResponse::fromJsonString($this->serialize($data));
    }
}
