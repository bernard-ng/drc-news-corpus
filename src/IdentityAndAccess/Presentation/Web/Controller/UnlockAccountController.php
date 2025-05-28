<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Presentation\Web\Controller;

use App\IdentityAndAccess\Application\UseCase\Command\UnlockAccount;
use App\IdentityAndAccess\Domain\Model\ValueObject\Secret\GeneratedToken;
use App\SharedKernel\Presentation\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

/**
 * Class UnlockAccountController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class UnlockAccountController extends AbstractController
{
    #[Route(
        path: '/api/account/unlock/{token}',
        name: 'identity_and_access_unlock_account',
        requirements: [
            'token' => Requirement::ASCII_SLUG,
        ],
        methods: ['GET']
    )]
    public function __invoke(string $token): JsonResponse
    {
        $token = new GeneratedToken($token);
        $this->handleCommand(new UnlockAccount($token));

        return new JsonResponse(status: 200);
    }
}
