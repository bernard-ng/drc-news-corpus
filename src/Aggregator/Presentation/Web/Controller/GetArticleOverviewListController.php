<?php

declare(strict_types=1);

namespace App\Aggregator\Presentation\Web\Controller;

use App\Aggregator\Application\ReadModel\ArticleOverviewList;
use App\Aggregator\Application\UseCase\Query\GetArticleOverviewList;
use App\Aggregator\Domain\Model\ValueObject\Filters\ArticleFilters;
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
#[Route('api/aggregator/articles', name: 'aggregator_articles_overviews', methods: ['GET'])]
final class GetArticleOverviewListController extends AbstractController
{
    public function __invoke(
        #[MapQueryString] Page $page,
        #[MapQueryString] ArticleFilters $filters
    ): JsonResponse {
        /** @var ArticleOverviewList $articles */
        $articles = $this->handleQuery(new GetArticleOverviewList($filters, $page));

        return JsonResponse::fromJsonString($this->serialize($articles));
    }
}
