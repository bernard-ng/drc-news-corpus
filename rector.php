<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector;
use Rector\Config\RectorConfig;
use Rector\Exception\Configuration\InvalidConfigurationException;
use Rector\ValueObject\PhpVersion;

try {
    return RectorConfig::configure()
        ->withPaths([
            __DIR__ . '/src',
            __DIR__ . '/tests',
        ])
        ->withImportNames(
            importDocBlockNames: false,
            importShortClasses: false,
            removeUnusedImports: true
        )
        ->withPhpVersion(PhpVersion::PHP_84)
        ->withPhpSets(php84: true)
        ->withPreparedSets(
            deadCode: true,
            codeQuality: true,
            codingStyle: true,
            typeDeclarations: true,
            privatization: true,
            instanceOf: true,
            earlyReturn: true,
            doctrineCodeQuality: true
        )
        ->withSkip([
            CatchExceptionNameMatchingTypeRector::class
        ]);
} catch (InvalidConfigurationException $e) {
    echo $e->getMessage();
    exit(1);
}
