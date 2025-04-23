<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Infrastructure\Secret;

use App\IdentityAndAccess\Domain\Model\ValueObject\Secret\GeneratedCode;
use App\IdentityAndAccess\Domain\Model\ValueObject\Secret\GeneratedToken;
use App\IdentityAndAccess\Domain\Service\SecretGenerator;
use Random\Randomizer;

/**
 * Class RandomizerSecretGenerator.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class RandomizerSecretGenerator implements SecretGenerator
{
    private const string ALLOWED_CHARACTERS = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    #[\Override]
    public function generateToken(int $length = 60): GeneratedToken
    {
        $value = new Randomizer()
            ->getBytesFromString(self::ALLOWED_CHARACTERS, $length);

        return new GeneratedToken($value);
    }

    #[\Override]
    public function generateCode(int $length = 6): GeneratedCode
    {
        $min = 10 ** ($length - 1);
        $max = 10 ** $length - 1;

        $value = new Randomizer()
            ->getInt($min, $max);

        return new GeneratedCode((string) $value);
    }
}
