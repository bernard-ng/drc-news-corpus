<?php

declare(strict_types=1);

namespace App\Aggregator\Presentation\Web\Controller;

use App\Aggregator\Application\ReadModel\Source\SourceOverviewList;
use App\Aggregator\Application\UseCase\Query\GetSourceOverviewList;
use App\SharedKernel\Domain\Model\Filters\FiltersQuery;
use App\SharedKernel\Domain\Model\ValueObject\Page;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class GetSourceOverviewListController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[AsController]
final class GetSourceOverviewListController extends AbstractController
{
    #[Route(
        path: '/api/aggregator/sources',
        name: 'aggregator_source_overview_list',
        methods: ['GET']
    )]
    public function __invoke(
        #[MapQueryString] Page $page,
        #[MapQueryString] FiltersQuery $filters
    ): JsonResponse {
        $securityUser = $this->getSecurityUser();

        /** @var SourceOverviewList $stats */
        $stats = $this->handleQuery(new GetSourceOverviewList($filters, $page, $securityUser->userId));

        return JsonResponse::fromJsonString($this->serialize($stats));
    }
}
