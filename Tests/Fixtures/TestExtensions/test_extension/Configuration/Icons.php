<?php

return [
    'test_extension-plugin-testplugin' => [
        'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        'source' => 'EXT:test_extension/Resources/Public/Icons/user_plugin_testplugin.svg'
    ],
    'test_extension-module-testmodule1' => [
        'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        'source' => 'EXT:test_extension/Resources/Public/Icons/user_mod_testmodule1.svg'
    ],
];
