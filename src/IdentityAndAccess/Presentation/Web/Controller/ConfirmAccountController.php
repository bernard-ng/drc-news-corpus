<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Presentation\Web\Controller;

use App\IdentityAndAccess\Application\UseCase\Command\ConfirmAccount;
use App\IdentityAndAccess\Domain\Model\ValueObject\Secret\GeneratedToken;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class UnlockAccountController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[Route('/api/account/confirm/{token}', name: 'identity_and_access_confirm_account', methods: ['GET'])]
final class ConfirmAccountController extends AbstractController
{
    public function __invoke(string $token): JsonResponse
    {
        $token = new GeneratedToken($token);
        $this->handleCommand(new ConfirmAccount($token));

        return new JsonResponse(status: 200);
    }
}
