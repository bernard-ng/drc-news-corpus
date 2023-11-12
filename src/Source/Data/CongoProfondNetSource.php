<?php

declare(strict_types=1);

namespace App\Source\Data;

use App\Source\AbstractSource;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

/**
 * Class JuricafOrgSource.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[AsTaggedItem('app.data_source')]
final readonly class CongoProfondNetSource extends AbstractSource
{
    public const URL = 'https://congoprofond.net';

    public const ID = 'congoprofond.net';

    public function supports(string $source): bool
    {
        return $source === self::ID;
    }

    public function process(SymfonyStyle $io, int $start, int $end, string $filename = self::ID, ?array $categories = []): void
    {
    }
}
