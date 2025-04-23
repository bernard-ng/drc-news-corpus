<?php

declare(strict_types=1);

namespace App\IdentityAndAccess\Domain\Model\ValueObject;

/**
 * Enum TokenPurpose.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
enum TokenPurpose: string
{
    case CONFIRM_ACCOUNT = 'confirm_account';
    case PASSWORD_RESET = 'password_reset';
    case UNLOCK_ACCOUNT = 'unlock_account';
    case DELETE_ACCOUNT = 'delete_account';
}
