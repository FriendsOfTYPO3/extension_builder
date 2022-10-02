<?php

return [
    'web_ExtensionBuilder' => [
        'parent' => 'tools',
        'position' => ['after' => 'tools_ExtensionmanagerExtensionmanager'],
        'access' => 'admin',
        'workspaces' => 'live',
        'iconIdentifier' => 'extensionbuilder-module',
        'path' => '/module/tools/extensionBuilder',
        'labels' => 'LLL:EXT:extension_builder/Resources/Private/Language/locallang_mod.xlf',
        'extensionName' => 'ExtensionBuilder',
        'controllerActions' => [
            BuilderModuleController::class => [
                'domainmodelling',
                'index',
                'help',
                'dispatchRpc'
            ],
        ],
    ],
];
