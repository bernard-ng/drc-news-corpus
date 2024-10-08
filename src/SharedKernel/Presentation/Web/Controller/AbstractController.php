<?php

declare(strict_types=1);

namespace App\SharedKernel\Presentation\Web\Controller;

use App\SharedKernel\Application\Bus\CommandBus;
use App\SharedKernel\Application\Bus\QueryBus;
use App\SharedKernel\Domain\Model\Exception\UserFacingError;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyController;
use Symfony\Component\HttpFoundation\Response;
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

        return $subscribedServices;
    }

    #[\Override]
    public function render(string $view, array $parameters = [], ?Response $response = null): Response
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

    protected function addErrorFlash(string $id, string $domain = 'messages', array $params = []): void
    {
        $this->addFlash('error', $this->trans($id, $params, $domain));
    }

    protected function addSuccessFlash(string $id, string $domain = 'messages', array $params = []): void
    {
        $this->addFlash('success', $this->trans($id, $params, $domain));
    }

    protected function addExceptionFlash(\Throwable $e, int $status): void
    {
        $message = match (true) {
            $e instanceof UserFacingError => $this->trans(
                $e->translationId(),
                $e->translationParameters(),
                $e->translationDomain()
            ),
            default => $this->trans('error.generic'),
        };

        $this->addFlash('error', $message);
        $this->setStatus($status);
    }

    protected function setStatus(int $status): void
    {
        $this->response = new Response(status: $status);
    }
}
