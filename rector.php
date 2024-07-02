<?php
declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Ssch\TYPO3Rector\Set\Typo3SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__
    ]);

    // register a single rule
    // $rectorConfig->rule(InlineConstructorDefaultToPropertyRector::class);

    // define sets of rules
    $rectorConfig->sets([
        Typo3SetList::TYPO3_12,
        Typo3SetList::TYPOSCRIPT_120,
        Typo3SetList::TYPOSCRIPT_CONDITIONS_104,
        LevelSetList::UP_TO_PHP_82,
        Typo3SetList::TCA_120,
        Typo3SetList::DATABASE_TO_DBAL,
        Typo3SetList::EXTBASE_COMMAND_CONTROLLERS_TO_SYMFONY_COMMANDS,
        Typo3SetList::REGISTER_ICONS_TO_ICON,
        SetList::CODE_QUALITY,
        // SetList::CODING_STYLE,
        // SetList::DEAD_CODE,
        // SetList::EARLY_RETURN,
        // SetList::NAMING,
        // SetList::PRIVATIZATION,
        // SetList::PSR_4,
        // SetList::TYPE_DECLARATION,
    ]);
};
