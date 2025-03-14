<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Domain\Model\Entity\Feature;

use App\IdentityAndAccess\Domain\Exception\InvalidPassword;
use App\IdentityAndAccess\Domain\Exception\PasswordDefined;
use App\IdentityAndAccess\Domain\Model\Event\PasswordCreated;
use App\IdentityAndAccess\Domain\Model\Event\PasswordForgotten;
use App\IdentityAndAccess\Domain\Model\Event\PasswordReset;
use App\IdentityAndAccess\Domain\Model\Event\PasswordUpdated;
use App\IdentityAndAccess\Domain\Model\ValueObject\Secret\GeneratedCode;
use App\IdentityAndAccess\Domain\Model\ValueObject\Secret\TimedToken;
use App\IdentityAndAccess\Domain\Service\PasswordHasher;

/**
 * Class PasswordFeature.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
trait PasswordFeature
{
    public function passwordForgotten(TimedToken $token): void
    {
        $this->passwordResetToken = $token;
        $this->emitEvent(
            new PasswordForgotten(
                $this->id,
                $this->passwordResetToken,
            )
        );
    }

    public function resetPassword(TimedToken $token, string $password, PasswordHasher $passwordHasher): void
    {
        $this->passwordResetToken?->assertValidAgainst($token);

        $this->password = $passwordHasher->hash($this, $password);
        $this->passwordResetToken = null;
        $this->emitEvent(new PasswordReset($this->id));
    }

    public function updatePassword(string $current, string $new, PasswordHasher $passwordHasher): self
    {
        if ($this->password === null || ! $passwordHasher->verify($this, $current)) {
            throw new InvalidPassword();
        }

        $this->password = $passwordHasher->hash($this, $new);
        $this->emitEvent(new PasswordUpdated($this->id));

        return $this;
    }

    public function definePassword(GeneratedCode|string $password, PasswordHasher $passwordHasher): self
    {
        if ($this->password !== null) {
            throw new PasswordDefined();
        }

        $this->password = $passwordHasher->hash($this, (string) $password);
        $this->updatedAt = new \DateTimeImmutable();

        if ($password instanceof GeneratedCode) {
            $this->emitEvent(new PasswordCreated($this->id, $password));
        }

        return $this;
    }
}
