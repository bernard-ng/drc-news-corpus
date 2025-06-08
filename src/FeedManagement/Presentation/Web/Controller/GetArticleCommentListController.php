<?php

declare(strict_types=1);

namespace App\FeedManagement\Presentation\Web\Controller;

use App\Aggregator\Domain\Model\Identity\ArticleId;
use App\FeedManagement\Application\ReadModel\CommentList;
use App\FeedManagement\Application\UseCase\Query\GetArticleCommentList;
use App\SharedKernel\Domain\Model\Pagination\Page;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

/**
 * Class GetArticleCommentListController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class GetArticleCommentListController extends AbstractController
{
    #[Route(
        path: '/api/feed/articles/{articleId}/comments',
        name: 'feed_management_article_comment_list',
        requirements: [
            'articleId' => Requirement::UUID_V7,
        ],
        methods: ['GET']
    )]
    public function __invoke(ArticleId $articleId, #[MapQueryString] Page $page): JsonResponse
    {
        /** @var CommentList $data */
        $data = $this->handleQuery(new GetArticleCommentList($articleId, $page));

        return JsonResponse::fromJsonString($this->serialize($data));
    }
}
