<?php

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
return [
    // Register extensionbuilder module icon
    'extensionbuilder-module' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:extension_builder/Resources/Public/Icons/Extension.svg',
    ],
];
