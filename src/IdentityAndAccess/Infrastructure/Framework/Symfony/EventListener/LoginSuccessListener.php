<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Infrastructure\Framework\Symfony\EventListener;

use App\IdentityAndAccess\Application\UseCase\Command\RegisterLoginSuccess;
use App\IdentityAndAccess\Infrastructure\Framework\Symfony\Security\SecurityUser;
use App\SharedKernel\Application\Bus\CommandBus;
use App\SharedKernel\Domain\Model\ValueObject\Tracking\ClientProfile;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

/**
 * Class LoginSuccessListener.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[AsEventListener(LoginSuccessEvent::class)]
final readonly class LoginSuccessListener
{
    public function __construct(
        private CommandBus $commandBus,
    ) {
    }

    public function __invoke(LoginSuccessEvent $event): void
    {
        /** @var SecurityUser|null $user */
        $user = $event->getAuthenticatedToken()->getUser();

        if ($user !== null) {
            $profile = new ClientProfile(
                IpUtils::anonymize((string) $event->getRequest()->getClientIp(), 1),
                $event->getRequest()->headers->get('User-Agent'),
                $event->getRequest()->server->all()
            );

            $this->commandBus->handle(new RegisterLoginSuccess($user->userId, $profile));
        }
    }
}
