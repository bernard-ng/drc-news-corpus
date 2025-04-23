<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Presentation\Web\Controller;

use App\IdentityAndAccess\Application\UseCase\Command\RequestPassword;
use App\IdentityAndAccess\Presentation\Web\WriteModel\RequestPasswordModel;
use App\SharedKernel\Domain\Model\ValueObject\Email;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class RequestPasswordController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[Route('/api/password/request', name: 'identity_and_access_request_password', methods: ['POST'])]
final class RequestPasswordController extends AbstractController
{
    public function __invoke(#[MapRequestPayload] RequestPasswordModel $model): JsonResponse
    {
        $email = Email::from($model->email);
        $this->handleCommand(new RequestPassword($email));

        return new JsonResponse(status: 200);
    }
}
