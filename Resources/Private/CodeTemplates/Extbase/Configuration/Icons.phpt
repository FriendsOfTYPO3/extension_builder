<?php

return [<f:for each="{extension.plugins}" as="plugin">
    '{extension.extensionKey}-plugin-{plugin.key}' => [
        'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        'source' => 'EXT:{extension.extensionKey}/Resources/Public/Icons/user_plugin_{plugin.key}.svg'
    ],</f:for><f:for each="{extension.BackendModules}" as="backendModule">
    '{extension.extensionKey}-module-{backendModule.key}' => [
        'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        'source' => 'EXT:{extension.extensionKey}/Resources/Public/Icons/user_mod_{backendModule.key}.svg'
    ],</f:for>
];
