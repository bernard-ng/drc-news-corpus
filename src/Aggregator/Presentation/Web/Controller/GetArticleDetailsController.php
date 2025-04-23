<?php

declare(strict_types=1);

namespace App\Aggregator\Presentation\Web\Controller;

use App\Aggregator\Application\ReadModel\ArticleDetails;
use App\Aggregator\Application\UseCase\Query\GetArticleDetails;
use App\Aggregator\Domain\Exception\ArticleNotFound;
use App\Aggregator\Domain\Model\Entity\Identity\ArticleId;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

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
    public function __invoke(ArticleId $id): JsonResponse
    {
        try {
            /** @var ArticleDetails $article */
            $article = $this->handleQuery(new GetArticleDetails($id));
        } catch (ArticleNotFound $e) {
            throw $this->createNotFoundException(previous: $e);
        }

        return JsonResponse::fromJsonString($this->serialize($article));
    }
}
