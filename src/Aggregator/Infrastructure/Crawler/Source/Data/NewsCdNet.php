<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Crawler\Source\Data;

use App\Aggregator\Infrastructure\Crawler\Source\WordPressJson;

/**
 * Class NewsCdNet.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class NewsCdNet extends WordPressJson
{
    public const string URL = 'https://newscd.net';

    public const string ID = 'newscd.net';
}
