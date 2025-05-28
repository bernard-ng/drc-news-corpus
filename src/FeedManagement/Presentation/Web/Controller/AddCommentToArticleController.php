<?php

declare(strict_types=1);

namespace App\FeedManagement\Presentation\Web\Controller;

use App\Aggregator\Domain\Model\Identity\ArticleId;
use App\FeedManagement\Application\UseCase\Command\AddCommentToArticle;
use App\FeedManagement\Presentation\WriteModel\AddCommentToArticleModel;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

/**
 * Class AddCommentToArticleController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class AddCommentToArticleController extends AbstractController
{
    #[Route(
        path: '/api/feed/articles/{articleId}/comments',
        name: 'feed_management_add_comment_to_article',
        requirements: [
            'articleId' => Requirement::UUID_V7,
        ],
        methods: ['POST']
    )]
    public function __invoke(ArticleId $articleId, #[MapRequestPayload] AddCommentToArticleModel $model): JsonResponse
    {
        $securityUser = $this->getSecurityUser();
        $this->handleCommand(new AddCommentToArticle($securityUser->userId, $articleId, $model->content));

        return new JsonResponse(status: Response::HTTP_CREATED);
    }
}
