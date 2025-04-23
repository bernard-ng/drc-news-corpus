<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Presentation\Web\WriteModel;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class RegisterModel.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class UpdatePasswordModel
{
    #[Assert\NotBlank]
    public string $current;

    #[Assert\NotBlank]
    #[Assert\Length(max: 4096)]
    #[Assert\PasswordStrength]
    public string $password;

    #[Assert\EqualTo(
        propertyPath: 'password',
        message: 'identity_and_access.exceptions.passwords_do_not_match',
    )]
    public string $confirm;
}
