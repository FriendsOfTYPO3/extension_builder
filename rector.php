<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Ssch\TYPO3Rector\Set\Typo3SetList;

return RectorConfig::configure()
    ->withPaths([__DIR__ . '/Classes', __DIR__ . '/Tests'])
    ->withSets([
        Typo3SetList::TYPO3_12,
    ]);
