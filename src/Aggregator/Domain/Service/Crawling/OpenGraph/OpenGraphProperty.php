<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Service\Crawling\OpenGraph;

/**
 * Class Property.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class OpenGraphProperty
{
    public const string AUDIO = 'og:audio';

    public const string AUDIO_SECURE_URL = 'og:audio:secure_url';

    public const string AUDIO_TYPE = 'og:audio:type';

    public const string AUDIO_URL = 'og:audio:url';

    public const string DESCRIPTION = 'og:description';

    public const string DETERMINER = 'og:determiner';

    public const string IMAGE = 'og:image';

    public const string IMAGE_HEIGHT = 'og:image:height';

    public const string IMAGE_SECURE_URL = 'og:image:secure_url';

    public const string IMAGE_TYPE = 'og:image:type';

    public const string IMAGE_URL = 'og:image:url';

    public const string IMAGE_WIDTH = 'og:image:width';

    public const string IMAGE_USER_GENERATED = 'og:image:user_generated';

    public const string LOCALE = 'og:locale';

    public const string LOCALE_ALTERNATE = 'og:locale:alternate';

    public const string RICH_ATTACHMENT = 'og:rich_attachment';

    public const string SEE_ALSO = 'og:see_also';

    public const string SITE_NAME = 'og:site_name';

    public const string TITLE = 'og:title';

    public const string TYPE = 'og:type';

    public const string UPDATED_TIME = 'og:updated_time';

    public const string URL = 'og:url';

    public const string VIDEO = 'og:video';

    public const string VIDEO_HEIGHT = 'og:video:height';

    public const string VIDEO_SECURE_URL = 'og:video:secure_url';

    public const string VIDEO_TYPE = 'og:video:type';

    public const string VIDEO_URL = 'og:video:url';

    public const string VIDEO_WIDTH = 'og:video:width';

    public function __construct(
        public string $key,
        public mixed $value,
    ) {
    }
}
