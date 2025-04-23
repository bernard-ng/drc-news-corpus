<?php

declare(strict_types=1);

namespace App\Aggregator\Presentation\Web\Controller;

use App\Aggregator\Application\ReadModel\ArticleList;
use App\Aggregator\Application\UseCase\Query\GetArticleList;
use App\Aggregator\Domain\Model\ValueObject\Filters\ArticleFilters;
use App\SharedKernel\Domain\Model\ValueObject\Page;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class GetArticlesController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class GetArticlesController extends AbstractController
{
    #[Route('api/aggregator/articles', name: 'aggregator_articles', methods: ['GET'])]
    public function __invoke(
        #[MapQueryString]
        Page $page,
        #[MapQueryString]
        ArticleFilters $filters
    ): JsonResponse {
        /** @var ArticleList $articles */
        $articles = $this->handleQuery(new GetArticleList($filters, $page));

        return JsonResponse::fromJsonString($this->serialize($articles));
    }
}
