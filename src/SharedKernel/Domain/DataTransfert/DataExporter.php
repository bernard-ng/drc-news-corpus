<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\DataTransfert;

/**
 * Interface DataExporter.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface DataExporter
{
    public function export(iterable $data, TransfertSetting $setting = new TransfertSetting()): \SplFileObject;
}
