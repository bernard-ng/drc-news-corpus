<?php

declare(strict_types=1);

namespace App\FeedManagement\Domain\Exception;

use App\IdentityAndAccess\Domain\Model\Identity\UserId;
use App\SharedKernel\Domain\Exception\UserFacingError;

/**
 * Class SourceAlreadyFollowed.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class SourceAlreadyFollowed extends \DomainException implements UserFacingError
{
    public static function with(UserId $userId, string $source): self
    {
        return new self(sprintf('User %s already follows source %s', $userId->toString(), $source));
    }

    public function translationId(): string
    {
        return 'feed_management.exceptions.source_already_followed';
    }

    public function translationParameters(): array
    {
        return [];
    }

    public function translationDomain(): string
    {
        return 'feed_management';
    }
}
