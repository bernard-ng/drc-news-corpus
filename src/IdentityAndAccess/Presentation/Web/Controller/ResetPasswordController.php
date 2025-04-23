<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Presentation\Web\Controller;

use App\IdentityAndAccess\Application\UseCase\Command\ResetPassword;
use App\IdentityAndAccess\Domain\Model\ValueObject\Secret\GeneratedToken;
use App\IdentityAndAccess\Presentation\Web\WriteModel\ResetPasswordModel;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class RequestPasswordController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[Route('/api/password/reset/{token}', name: 'identity_and_access_reset_password', methods: ['POST'])]
final class ResetPasswordController extends AbstractController
{
    public function __invoke(#[MapRequestPayload] ResetPasswordModel $model, string $token): JsonResponse
    {
        $token = new GeneratedToken($token);
        $this->handleCommand(new ResetPassword($token, $model->password));

        return new JsonResponse(status: 200);
    }
}
