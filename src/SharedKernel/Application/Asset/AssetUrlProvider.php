<?php

declare(strict_types=1);

namespace App\SharedKernel\Application\Asset;

/**
 * Class AssetUrlProvider.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface AssetUrlProvider
{
    public function getUrl(string $id, AssetType $type): ?string;
}
