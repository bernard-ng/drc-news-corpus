<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])

    ->withRules([
        NoUnusedImportsFixer::class,
    ])
    ->withConfiguredRule(MethodArgumentSpaceFixer::class, [
        'on_multiline' => 'ensure_fully_multiline',
        'attribute_placement' => 'same_line'
    ])
    ->withSkip([
        ConcatSpaceFixer::class
    ])
    ->withPreparedSets(
        psr12: true,
        common: true,
        cleanCode: true,
    );