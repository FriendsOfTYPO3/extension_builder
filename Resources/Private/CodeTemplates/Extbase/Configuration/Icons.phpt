<?php

return [<f:for each="{extension.plugins}" as="plugin">
    '{extension.extensionKey}-plugin-{plugin.key}' => [
        'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        'source' => 'EXT:{extension.extensionKey}/Resources/Public/Icons/user_plugin_{plugin.key}.svg'
    ],</f:for>
];
