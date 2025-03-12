<?php

declare(strict_types=1);

namespace App\Aggregator\Presentation\Web\Controller;

use App\Aggregator\Application\ReadModel\Articles;
use App\Aggregator\Application\UseCase\Query\GetArticlesQuery;
use App\Aggregator\Domain\ValueObject\Filters\ArticleFilters;
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
        /** @var Articles $articles */
        $articles = $this->handleQuery(new GetArticlesQuery($filters, $page));

        return JsonResponse::fromJsonString($this->serialize($articles));
    }
}
