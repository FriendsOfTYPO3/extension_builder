<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'extension-builder-module' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:extension_builder/Resources/Public/Icons/Extension.svg',
    ],
];
