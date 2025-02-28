<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\DataTransfert;

/**
 * Class TransfertSetting.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class TransfertSetting
{
    public function __construct(
        public ?string $filename = null,
        public ?string $type = null,
        public string $format = 'csv'
    ) {
    }
}
