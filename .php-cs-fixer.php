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
    'trailing_comma_in_multiline' => false,
    'new_with_braces' => true,
    'header_comment' => false,
    'no_unused_imports' => true,
    'visibility_required' => true,
    'phpdoc_trim' => true,
    'ordered_imports' => false,
    'function_declaration' => true,
    'fully_qualified_strict_types' => true,
    'yoda_style' => false,
    'global_namespace_import' => true,
    'no_whitespace_in_blank_line' => true,
    'blank_line_after_opening_tag' => true,
    'no_extra_blank_lines' => true,
    'cast_spaces' => true,
    'blank_line_after_namespace' => true,
    'phpdoc_no_empty_return' => true,
    'elseif' => true,
    'braces' => true,
    'void_return' => true,
    'is_null' => true,
    // 'global_namespace_import' => ['import_classes' => true, 'import_constants' => true, 'import_functions' => true],
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
