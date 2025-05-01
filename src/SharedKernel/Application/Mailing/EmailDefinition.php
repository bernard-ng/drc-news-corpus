<?php

declare(strict_types=1);

namespace App\SharedKernel\Application\Mailing;

use App\SharedKernel\Domain\Model\ValueObject\Email;

/**
 * Interface EmailDefinition.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface EmailDefinition
{
    public function recipient(): Email;

    public function subject(): string;

    public function subjectVariables(): array;

    public function template(): string;

    public function templateVariables(): array;

    public function locale(): string;

    public function getDomain(): string;
}
