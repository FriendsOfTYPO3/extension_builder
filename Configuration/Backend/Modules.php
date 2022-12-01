<?php

return [
    'extension_builder' => [
        'parent' => 'tools',
        'position' => [''],
        'access' => 'user,group',
        'iconIdentifier' => 'extensionbuilder-module',
        'path' => '/module/tools/extensionBuilder',
        'labels' => 'LLL:EXT:extension_builder/Resources/Private/Language/locallang_mod.xlf',
        'extensionName' => 'ExtensionBuilder',
        'controllerActions' => [
            \EBT\ExtensionBuilder\Controller\BuilderModuleController::class => [
                'overview',
                'extensionModelling',
                'help',
                'dispatchRpc'
            ],
        ],
    ],
];
