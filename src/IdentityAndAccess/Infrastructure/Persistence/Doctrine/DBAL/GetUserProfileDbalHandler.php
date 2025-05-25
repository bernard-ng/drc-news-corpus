<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Infrastructure\Persistence\Doctrine\DBAL;

use App\IdentityAndAccess\Application\ReadModel\UserProfile;
use App\IdentityAndAccess\Application\UseCase\Query\GetUserProfile;
use App\IdentityAndAccess\Application\UseCase\QueryHandler\GetUserProfileHandler;
use App\IdentityAndAccess\Domain\Exception\UserNotFound;
use App\IdentityAndAccess\Domain\Model\Identity\UserId;
use App\SharedKernel\Domain\Model\ValueObject\EmailAddress;
use App\SharedKernel\Infrastructure\Persistence\Doctrine\DBAL\Mapping;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;

/**
 * Class GetUserProfileDbalHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class GetUserProfileDbalHandler implements GetUserProfileHandler
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function __invoke(GetUserProfile $query): UserProfile
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('u.id', 'u.name', 'u.email', 'u.created_at')
            ->from('user', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $query->userId->toBinary(), ParameterType::INTEGER);

        /** @var array<string, mixed>|false $data */
        $data = $qb->executeQuery()->fetchAssociative();

        if ($data === false) {
            throw UserNotFound::withId($query->userId);
        }

        return new UserProfile(
            UserId::fromBinary($data['id']),
            Mapping::string($data, 'name'),
            EmailAddress::from(Mapping::string($data, 'email')),
            Mapping::nullableDateTime($data, 'updated_at'),
            Mapping::dateTime($data, 'created_at')
        );
    }
}
