<?php

use FIXTURE\TestExtension\Controller\MainController;
use TYPO3\CMS\Backend\Security\SudoMode\Access\AccessLifetime;

return [
    'web_TestExtension' => [
        'parent' => 'web',
        'position' => ['after' => '*'],
        'access' => 'admin',
        'workspaces' => 'live',
        'iconIdentifier' => 'testextension-module',
        'path' => '/module/tools/testExtension',
        'labels' => 'LLL:EXT:extension_builder/Resources/Private/Language/locallang_mod.xlf',
        'extensionName' => 'TestExtension',
        'controllerActions' => [
            MainController::class => [
                'list',
                'show',
                'new',
                'create',
                'edit',
                'update',
                'delete',
                'custom',
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
