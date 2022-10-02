<?php

return [
    'web_ExtkeyExample' => [
        'parent' => 'tools',
        'position' => [''],
        'access' => 'user,group',
        'workspaces' => 'live',
        'iconIdentifier' => 'extensionbuilder-module',
        'path' => '/module/tools/extensionBuilder',
        'labels' => 'LLL:EXT:extension_builder/Resources/Private/Language/locallang_mod.xlf',
        'extensionName' => 'ExtensionBuilder',
        'controllerActions' => [
            \EBT\ExtensionBuilder\Controller\BuilderModuleController::class => [
                'index',
                'domainmodelling',
                'dispatchRpc'
            ],
        ],
    ],
];
