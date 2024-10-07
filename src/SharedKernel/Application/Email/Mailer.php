<?php

declare(strict_types=1);

namespace App\SharedKernel\Application\Email;

/**
 * Interface Mailer.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface Mailer
{
    public function send(Definition $email): void;
}
