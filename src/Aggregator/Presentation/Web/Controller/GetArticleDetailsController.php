<?php

declare(strict_types=1);

namespace App\Aggregator\Presentation\Web\Controller;

use App\Aggregator\Application\ReadModel\Article;
use App\Aggregator\Application\UseCase\Query\GetArticleDetailsQuery;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Uid\Uuid;

/**
 * Class GetArticlesController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class GetArticleDetailsController extends AbstractController
{
    #[Route('api/aggregator/articles/{id}', name: 'aggregator_articles_details', requirements: [
        'id' => Requirement::UUID,
    ], methods: ['GET'])]
    public function __invoke(Uuid $id): JsonResponse
    {
        /** @var Article $article */
        $article = $this->handleQuery(new GetArticleDetailsQuery($id));

        return JsonResponse::fromJsonString($this->serialize($article));
    }
}
