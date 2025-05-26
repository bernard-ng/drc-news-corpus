<?php

declare(strict_types=1);

namespace App\SharedKernel\Presentation\Web\EventListener;

use App\Aggregator\Domain\Exception\ArticleNotFound;
use App\Aggregator\Domain\Exception\SourceNotFound;
use App\FeedManagement\Domain\Exception\BookmarkedArticleNotFound;
use App\FeedManagement\Domain\Exception\BookmarkNotFound;
use App\FeedManagement\Domain\Exception\CommentNotFound;
use App\FeedManagement\Domain\Exception\FollowedSourceNotFound;
use App\IdentityAndAccess\Domain\Exception\PermissionNotGranted;
use App\IdentityAndAccess\Domain\Exception\UserNotFound;
use App\SharedKernel\Domain\Exception\InvalidArgument;
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
    private const array NOT_FOUND_EXCEPTIONS = [
        ArticleNotFound::class,
        SourceNotFound::class,
        UserNotFound::class,
        CommentNotFound::class,
        BookmarkNotFound::class,
        FollowedSourceNotFound::class,
        BookmarkedArticleNotFound::class,
    ];

    private const array BAD_REQUEST_EXCEPTIONS = [
        InvalidArgument::class,
    ];

    private const array FORBIDDEN_EXCEPTIONS = [
        PermissionNotGranted::class,
    ];

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

            $status = $this->getResponseStatus($exception);
            $response = new JsonResponse([
                'type' => 'https://symfony.com/errors/domain',
                'title' => $exception->translationId(),
                'detail' => $message,
                'status' => $status,
            ], $status);

            $event->setResponse($response);
        }
    }

    public function getResponseStatus(UserFacingError $exception): int
    {
        return match (true) {
            in_array($exception::class, self::NOT_FOUND_EXCEPTIONS) => Response::HTTP_NOT_FOUND,
            in_array($exception::class, self::BAD_REQUEST_EXCEPTIONS) => Response::HTTP_BAD_REQUEST,
            in_array($exception::class, self::FORBIDDEN_EXCEPTIONS) => Response::HTTP_FORBIDDEN,
            default => Response::HTTP_UNPROCESSABLE_ENTITY
        };
    }
}
