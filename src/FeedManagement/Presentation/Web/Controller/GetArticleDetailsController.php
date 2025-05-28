<?php

declare(strict_types=1);

namespace App\FeedManagement\Presentation\Web\Controller;

use App\Aggregator\Domain\Model\Identity\ArticleId;
use App\FeedManagement\Application\UseCase\Query\GetArticleDetails;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

/**
 * Class GetArticleDetailsController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class GetArticleDetailsController extends AbstractController
{
    #[Route(
        path: 'api/feed/articles/{articleId}',
        name: 'feed_management_article_details',
        requirements: [
            'articleId' => Requirement::UUID_V7,
        ],
        methods: ['GET']
    )]
    public function __invoke(ArticleId $articleId): JsonResponse
    {
        $securityUser = $this->getSecurityUser();
        $data = $this->handleQuery(new GetArticleDetails($articleId, $securityUser->userId));

        return JsonResponse::fromJsonString($this->serialize($data));
    }
}
