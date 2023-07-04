<?php

use EBT\ExtensionBuilder\Controller\BuilderModuleController;
use TYPO3\CMS\Backend\Security\SudoMode\Access\AccessLifetime;

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
        'routeOptions' => [
            'sudoMode' => [
                'group' => 'systemMaintainer',
                'lifetime' => AccessLifetime::veryShort,
            ],
        ],
    ],
];
