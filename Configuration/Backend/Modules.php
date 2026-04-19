<?php

declare(strict_types=1);

use EBT\ExtensionBuilder\Controller\BuilderModuleController;

return [
    'tools_extensionbuilder' => [
        'parent' => 'admin',
        'position' => ['after' => 'styleguide'],
        'access' => 'admin',
        'workspaces' => 'live',
        'path' => '/module/extensionbuilder',
        'iconIdentifier' => 'extension-builder-module',
        'labels' => 'LLL:EXT:extension_builder/Resources/Private/Language/locallang_mod.xlf',
        'extensionName' => 'ExtensionBuilder',
        'controllerActions' => [
            BuilderModuleController::class => ['index', 'domainmodelling', 'dispatchRpc'],
        ],
    ],
];
