<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Crawler\Source\Data;

use App\Aggregator\Infrastructure\Crawler\Source\WordPressJson;

/**
 * Class PoliticoCdAbstractSource.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class PoliticoCd extends WordPressJson
{
    public const string URL = 'https://politico.cd';

    public const string ID = 'politico.cd';
}
