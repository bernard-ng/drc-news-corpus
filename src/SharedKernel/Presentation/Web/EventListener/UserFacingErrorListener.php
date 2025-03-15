<?php

declare(strict_types=1);

namespace App\SharedKernel\Presentation\Web\EventListener;

use App\SharedKernel\Domain\Exception\UserFacingError;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserFacingErrorListener.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[AsEventListener(KernelEvents::EXCEPTION, priority: -1)]
final readonly class UserFacingErrorListener
{
    public function __construct(
        private TranslatorInterface $translator
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if ($exception instanceof UserFacingError) {
            $message = $this->translator->trans(
                $exception->translationId(),
                $exception->translationParameters(),
                $exception->translationDomain(),
            );

            $response = new JsonResponse([
                'type' => 'https://symfony.com/errors/domain',
                'title' => $exception->translationId(),
                'detail' => $message,
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ], status: Response::HTTP_UNPROCESSABLE_ENTITY);

            $event->setResponse($response);
        }
    }
}
