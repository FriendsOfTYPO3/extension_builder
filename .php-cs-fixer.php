<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    die('This script supports command line usage only. Please check your command.');
}

$config = \TYPO3\CodingStandards\CsFixerConfig::create()
    ->setHeader();

$config->addRules([
    'no_blank_lines_after_class_opening' => true,
    'self_accessor' => true,
    'visibility_required' => true,
    'fully_qualified_strict_types' => true,
    'void_return' => true,
    'is_null' => true,
    'global_namespace_import' => ['import_classes' => true, 'import_constants' => true, 'import_functions' => true],
]);

$config
    ->getFinder()
    ->name('*.php')
    ->in(__DIR__)
    ->exclude(
        [
            '.Build',
            '.github',
            'Configuration',
            'Documentation',
            'Resources',
            'Tests/Fixtures',
        ]
    )
    ->notName([
        'ext_localconf.php',
        'ext_tables.php',
        'ext_emconf.php',
        'SpycYAMLParser.php',
    ])
    ->depth('> 1')
;

return $config;
