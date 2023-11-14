<?php

declare(strict_types=1);

namespace App\Event;

use Symfony\Component\Stopwatch\StopwatchEvent;

final readonly class CrawleFinishedEvent
{
    public function __construct(
        public StopwatchEvent $event,
        public string $filename,
        public string $source
    ) {
    }
}
