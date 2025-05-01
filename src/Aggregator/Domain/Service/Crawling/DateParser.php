<?php

declare(strict_types=1);

namespace App\Aggregator\Domain\Service\Crawling;

/**
 * Class DateParser.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final readonly class DateParser
{
    public const array MONTHS = [
        'janvier' => '01',
        'février' => '02',
        'mars' => '03',
        'avril' => '04',
        'mai' => '05',
        'juin' => '06',
        'juillet' => '07',
        'août' => '08',
        'septembre' => '09',
        'octobre' => '10',
        'novembre' => '11',
        'décembre' => '12',
    ];

    public const array DAYS = [
        'dimanche' => '0',
        'lundi' => '1',
        'mardi' => '2',
        'mercredi' => '3',
        'jeudi' => '4',
        'vendredi' => '5',
        'samedi' => '6',
    ];

    public const string DEFAULT_DATE_FORMAT = 'Y-m-d H:i';

    /**
     * @throws \Throwable
     */
    public function createTimeStamp(
        string $date,
        ?string $format = null,
        ?string $pattern = null,
        ?string $replacement = null
    ): string {
        /** @var string $date */
        $date = strtr(strtr(strtolower($date), self::DAYS), self::MONTHS);
        if ($pattern !== null && $replacement !== null) {
            /** @var string $date */
            $date = preg_replace(
                pattern: $pattern,
                replacement: $replacement,
                subject: $date
            );
        }

        if ($format === 'c') {
            $date = str_replace('t', ' ', $date);
            $format = 'Y-m-d H:i:s';
        }

        $datetime = \DateTime::createFromFormat($format ?? self::DEFAULT_DATE_FORMAT, $date);

        return $datetime !== false ?
            $datetime->format('U') :
            new \DateTime('midnight')->format('U');
    }
}
