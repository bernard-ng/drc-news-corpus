<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Domain\Model\Repository;

use App\IdentityAndAccess\Domain\Model\Entity\VerificationToken;
use App\IdentityAndAccess\Domain\Model\ValueObject\Secret\GeneratedToken;
use App\IdentityAndAccess\Domain\Model\ValueObject\TokenPurpose;

/**
 * Interface LoginAttemptRepository.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface VerificationTokenRepository
{
    public function add(VerificationToken $verificationToken): void;

    public function remove(VerificationToken $verificationToken): void;

    public function getByToken(GeneratedToken $token, TokenPurpose $purpose): VerificationToken;
}
