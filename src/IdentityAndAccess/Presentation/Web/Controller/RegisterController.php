<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Presentation\Web\Controller;

use App\IdentityAndAccess\Application\UseCase\Command\Register;
use App\IdentityAndAccess\Presentation\Web\WriteModel\RegisterModel;
use App\SharedKernel\Domain\Model\ValueObject\EmailAddress;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class RegisterController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[Route('/api/register', name: 'identity_and_access_register', methods: ['POST'])]
final class RegisterController extends AbstractController
{
    public function __invoke(#[MapRequestPayload] RegisterModel $model): JsonResponse
    {
        $this->handleCommand(new Register(
            $model->name,
            EmailAddress::from($model->email),
            $model->password
        ));

        return new JsonResponse(status: 201);
    }
}
