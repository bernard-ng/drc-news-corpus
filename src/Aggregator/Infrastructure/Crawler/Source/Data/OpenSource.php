<?php

declare(strict_types=1);

namespace App\Aggregator\Infrastructure\Crawler\Source\Data;

use App\Aggregator\Infrastructure\Crawler\Source\WordPressJson;

/**
 * Class OpenSource.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class OpenSource extends WordPressJson
{
    public const array SOURCES = [
        'beto.cd' => 'https://beto.cd',
        'newscd.net' => 'https://newscd.net',
        'africanewsrdc.net' => 'https://www.africanewsrdc.net',
        'angazainstitute.ac.cd' => 'https://angazainstitute.ac.cd',
        'b-onetv.cd' => 'https://b-onetv.cd',
        'bukavufm.com' => 'https://bukavufm.com',
        'changement7.net' => 'https://changement7.net',
        'congoactu.net' => 'https://congoactu.net',
        'congoindependant.com' => 'https://www.congoindependant.com',
        'congoquotidien.com' => 'https://www.congoquotidien.com',
        'cumulard.cd' => 'https://www.cumulard.cd',
        'environews-rdc.net' => 'https://environews-rdc.net',
        'freemediardc.info' => 'https://www.freemediardc.info',
        'geopolismagazine.org' => 'https://geopolismagazine.org',
        'habarirdc.net' => 'https://habarirdc.net',
        'infordc.com' => 'https://infordc.com',
        'kilalopress.net' => 'https://kilalopress.net',
        'laprosperiteonline.net' => 'https://laprosperiteonline.net',
        'laprunellerdc.cd' => 'https://laprunellerdc.cd',
        'lesmedias.net' => 'https://lesmedias.net',
        'lesvolcansnews.net' => 'https://lesvolcansnews.net',
        'netic-news.net' => 'https://www.netic-news.net',
        'objectif-infos.cd' => 'https://objectif-infos.cd',
        'scooprdc.net' => 'https://scooprdc.net',
        'journaldekinshasa.com' => 'https://www.journaldekinshasa.com',
        'lepotentiel.cd' => 'https://lepotentiel.cd',
        'acturdc.com' => 'https://acturdc.com',
        'matininfos.net' => 'https://matininfos.net',
    ];

    private ?string $selectedSourceId = null;

    #[\Override]
    public function supports(string $source): bool
    {
        $supported = array_key_exists($source, self::SOURCES);
        if ($supported) {
            $this->selectedSourceId = $source;
        }

        return $supported;
    }

    #[\Override]
    protected function getId(): string
    {
        if ($this->selectedSourceId) {
            return $this->selectedSourceId;
        }

        throw new \RuntimeException('No source selected');
    }

    #[\Override]
    protected function getUrl(): string
    {
        if ($this->selectedSourceId) {
            return self::SOURCES[$this->selectedSourceId];
        }

        throw new \RuntimeException('No source selected');
    }
}
