<?php

declare(strict_types=1);

namespace App\Aggregator\Presentation\Web\Controller;

use App\Aggregator\Application\ReadModel\ArticleOverviewList;
use App\Aggregator\Application\UseCase\Query\GetArticleOverviewList;
use App\SharedKernel\Domain\Model\Filters\FiltersQuery;
use App\SharedKernel\Domain\Model\ValueObject\Page;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class GetArticleOverviewListController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[AsController]
final class GetArticleOverviewListController extends AbstractController
{
    #[Route(
        path: 'api/aggregator/articles',
        name: 'aggregator_article_overview_list',
        methods: ['GET']
    )]
    public function __invoke(
        #[MapQueryString] Page $page,
        #[MapQueryString] FiltersQuery $filters
    ): JsonResponse {
        $securityUser = $this->getSecurityUser();

        /** @var ArticleOverviewList $articles */
        $articles = $this->handleQuery(new GetArticleOverviewList($securityUser->userId, $filters, $page));

        return JsonResponse::fromJsonString($this->serialize($articles));
    }
}
