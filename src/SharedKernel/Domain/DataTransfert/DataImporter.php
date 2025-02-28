<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\DataTransfert;

/**
 * Class DataImporter.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface DataImporter
{
    public function import(\SplFileObject $file, TransfertSetting $setting = new TransfertSetting()): iterable;
}
