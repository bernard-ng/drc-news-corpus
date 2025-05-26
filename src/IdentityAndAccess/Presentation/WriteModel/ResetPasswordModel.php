<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Presentation\WriteModel;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class RequestPasswordModel.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class ResetPasswordModel
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 512)]
    #[Assert\PasswordStrength]
    public string $password;

    #[Assert\EqualTo(
        propertyPath: 'password',
        message: 'identity_and_access.exceptions.passwords_do_not_match',
    )]
    public string $confirm;
}
