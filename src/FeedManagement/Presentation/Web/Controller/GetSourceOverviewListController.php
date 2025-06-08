<?php

declare(strict_types=1);

namespace App\FeedManagement\Presentation\Web\Controller;

use App\FeedManagement\Application\UseCase\Query\GetSourceOverviewList;
use App\SharedKernel\Domain\Model\Pagination\Page;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class GetSourceOverviewListController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class GetSourceOverviewListController extends AbstractController
{
    #[Route(
        path: '/api/feed/sources',
        name: 'feed_management_source_overview_list',
        methods: ['GET']
    )]
    public function __invoke(#[MapQueryString] Page $page): JsonResponse
    {
        $securityUser = $this->getSecurityUser();
        $data = $this->handleQuery(new GetSourceOverviewList($securityUser->userId, $page));

        return JsonResponse::fromJsonString($this->serialize($data));
    }
}
