<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Domain\Service;

use App\IdentityAndAccess\Domain\Model\ValueObject\Secret\GeneratedCode;
use App\IdentityAndAccess\Domain\Model\ValueObject\Secret\TimedToken;
use Random\Randomizer;

/**
 * Class SecretGenerator.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class SecretGenerator
{
    private const string ALLOWED_CHARACTERS = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    public function generateToken(int $length = 8): TimedToken
    {
        $value = (new Randomizer())
            ->getBytesFromString(self::ALLOWED_CHARACTERS, $length);

        return new TimedToken($value);
    }

    public function generateCode(int $length = 6): GeneratedCode
    {
        $min = 10 ** ($length - 1);
        $max = 10 ** $length - 1;

        $value = (new Randomizer())->getInt($min, $max);

        return new GeneratedCode((string) $value);
    }
}
