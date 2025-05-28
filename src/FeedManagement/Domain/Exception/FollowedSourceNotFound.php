<?php

declare(strict_types=1);

namespace App\FeedManagement\Domain\Exception;

use App\Aggregator\Domain\Model\Identity\SourceId;
use App\IdentityAndAccess\Domain\Model\Identity\UserId;
use App\SharedKernel\Domain\Exception\UserFacingError;

/**
 * Class FollowedSourceNotFound.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class FollowedSourceNotFound extends \DomainException implements UserFacingError
{
    public static function with(UserId $userId, SourceId $sourceId): self
    {
        return new self(sprintf('User %s does not follow source %s', $userId->toString(), $sourceId->toString()));
    }

    public function translationId(): string
    {
        return 'feed_management.exceptions.followed_source_not_found';
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
