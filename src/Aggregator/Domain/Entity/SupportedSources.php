<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Entity;

/**
 * Class SupportedSources.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
enum SupportedSources: string
{
    case ACTUALITE = 'actualite.cd';
    case POLITICO = 'politico.cd';
    case SEPT_SUR_SEPT = '7sur7.cd';
    case RADIO_OKAPI = 'radiookapi.net';
    case MEDIA_CONGO = 'mediacongo.net';
    case ACP_CONGO = 'acp.cd';
}
