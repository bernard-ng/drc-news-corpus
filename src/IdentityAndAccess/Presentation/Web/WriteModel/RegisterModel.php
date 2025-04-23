<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Presentation\Web\WriteModel;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class RegisterModel.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class RegisterModel
{
    #[Assert\NotBlank]
    public string $name;

    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Length(max: 4096)]
    public string $password;
}
