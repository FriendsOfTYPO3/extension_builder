<?php

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
return [
    'test_extension-plugin-testplugin' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:test_extension/Resources/Public/Icons/user_plugin_testplugin.svg'
    ],
];
