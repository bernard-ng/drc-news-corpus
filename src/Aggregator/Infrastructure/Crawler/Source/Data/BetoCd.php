<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Crawler\Source\Data;

use App\Aggregator\Infrastructure\Crawler\Source\WordPressJson;

/**
 * Class BetoCd.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class BetoCd extends WordPressJson
{
    public const string URL = 'https://beto.cd';

    public const string ID = 'beto.cd';
}
