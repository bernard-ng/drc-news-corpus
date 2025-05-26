<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Presentation\WriteModel;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class RegisterModel.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class RegisterModel
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    public string $name;

    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Length(max: 512)]
    public string $password;
}
