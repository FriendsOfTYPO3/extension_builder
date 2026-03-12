<?php

declare(strict_types=1);

return [
    'web_TestExtensionTestmodule1' => [
        'parent' => 'web',
        'access' => 'user',
        'labels' => 'LLL:EXT:test_extension/Resources/Private/Language/locallang_testmodule1.xlf',
        'extensionName' => 'TestExtension',
        'controllerActions' => [
            'FIXTURE\TestExtension\Controller\MainController' => [
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
    ],
];
