<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Ssch\TYPO3Rector\Set\Typo3SetList;

return RectorConfig::configure()
    ->withPaths([__DIR__ . '/Classes', __DIR__ . '/Tests'])
    ->withSkip([__DIR__ . '/Tests/Fixtures'])
    ->withSets([
        Typo3SetList::TYPO3_13,
    ]);
