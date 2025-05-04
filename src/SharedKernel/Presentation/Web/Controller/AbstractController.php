<?php

declare(strict_types=1);

namespace App\SharedKernel\Presentation\Web\Controller;

use App\IdentityAndAccess\Infrastructure\Framework\Symfony\Security\SecurityUser;
use App\SharedKernel\Application\Messaging\CommandBus;
use App\SharedKernel\Application\Messaging\QueryBus;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AbstractController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
abstract class AbstractController extends SymfonyController
{
    protected ?Response $response = null;

    #[\Override]
    public static function getSubscribedServices(): array
    {
        $subscribedServices = parent::getSubscribedServices();

        $subscribedServices[] = CommandBus::class;
        $subscribedServices[] = QueryBus::class;
        $subscribedServices[] = TranslatorInterface::class;
        $subscribedServices[] = LoggerInterface::class;
        $subscribedServices[] = SerializerInterface::class;

        return $subscribedServices;
    }

    public function getSecurityUser(): SecurityUser
    {
        /** @var SecurityUser|null $user */
        $user = $this->getUser();

        if ($user === null) {
            throw $this->createAccessDeniedException(
                'You must be authenticated to access this resource.'
            );
        }

        return $user;
    }

    public function serialize(mixed $data, string $format = 'json', array $context = []): string
    {
        /** @var SerializerInterface $serializer */
        $serializer = $this->container->get(SerializerInterface::class);
        return $serializer->serialize($data, $format, $context);
    }

    #[\Override]
    protected function render(string $view, array $parameters = [], ?Response $response = null): Response
    {
        return parent::render($view, $parameters, $response ?? $this->response);
    }

    protected function handleCommand(object $command): mixed
    {
        /** @var CommandBus $commandBus */
        $commandBus = $this->container->get(CommandBus::class);
        return $commandBus->handle($command);
    }

    protected function handleQuery(object $query): mixed
    {
        /** @var QueryBus $queryBus */
        $queryBus = $this->container->get(QueryBus::class);
        return $queryBus->handle($query);
    }

    protected function trans(string $key, array $params = [], string $domain = 'messages'): string
    {
        /** @var TranslatorInterface $trans */
        $trans = $this->container->get(TranslatorInterface::class);
        return $trans->trans($key, $params, $domain);
    }

    protected function setStatus(int $status): void
    {
        $this->response = new Response(status: $status);
    }
}
