<?php

declare(strict_types=1);

namespace App\Aggregator\Presentation\Web\Controller;

use App\Aggregator\Application\ReadModel\ArticleDetails;
use App\Aggregator\Application\UseCase\Query\GetArticleDetails;
use App\Aggregator\Domain\Model\Entity\Identity\ArticleId;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

/**
 * Class GetArticleDetailsController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[AsController]
#[Route('api/aggregator/articles/{id}', name: 'aggregator_articles_details', requirements: [
    'id' => Requirement::UUID,
], methods: ['GET'])]
final class GetArticleDetailsController extends AbstractController
{
    public function __invoke(ArticleId $id): JsonResponse
    {
        /** @var ArticleDetails $article */
        $article = $this->handleQuery(new GetArticleDetails($id));

        return JsonResponse::fromJsonString($this->serialize($article));
    }
}
