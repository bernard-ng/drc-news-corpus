<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Presentation\Web\Controller;

use App\IdentityAndAccess\Application\UseCase\Command\UnlockAccount;
use App\IdentityAndAccess\Domain\Model\ValueObject\Secret\GeneratedToken;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class UnlockAccountController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[Route('/api/account/unlock/{token}', name: 'identity_and_access_unlock_account', methods: ['GET'])]
final class UnlockAccountController extends AbstractController
{
    public function __invoke(string $token): JsonResponse
    {
        $token = new GeneratedToken($token);
        $this->handleCommand(new UnlockAccount($token));

        return new JsonResponse(status: 200);
    }
}
