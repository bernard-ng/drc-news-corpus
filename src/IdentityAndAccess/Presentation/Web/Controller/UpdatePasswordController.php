<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Presentation\Web\Controller;

use App\IdentityAndAccess\Application\UseCase\Command\UpdatePassword;
use App\IdentityAndAccess\Presentation\WriteModel\UpdatePasswordModel;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class UpdatePasswordController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class UpdatePasswordController extends AbstractController
{
    #[Route(
        path: '/api/password/update',
        name: 'identity_and_access_update_password',
        methods: ['POST']
    )]
    public function __invoke(#[MapRequestPayload] UpdatePasswordModel $model): JsonResponse
    {
        $securityUser = $this->getSecurityUser();
        $this->handleCommand(new UpdatePassword(
            $securityUser->userId,
            $model->current,
            $model->password
        ));

        return new JsonResponse(status: 200);
    }
}
