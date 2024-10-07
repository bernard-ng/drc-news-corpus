<?php

declare(strict_types=1);

namespace App\SharedKernel\Application\Email;

use App\SharedKernel\Domain\Model\ValueObject\Email;

/**
 * Interface Definition.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface Definition
{
    public function recipient(): Email;

    public function senderName(): string;

    public function senderAddress(): string;

    public function subject(): string;

    public function subjectVariables(): array;

    public function template(): string;

    public function templateVariables(): array;

    public function locale(): ?string;

    public function getDomain(): string;
}
