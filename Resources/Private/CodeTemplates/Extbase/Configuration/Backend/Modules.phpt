<?php

return [<f:for each="{extension.backendModules}" as="backendModule">
    '{backendModule.key}' => [
            'parent' => '{backendModule.mainModule}',
            'position' => ['bottom'],
            'access' => 'user',
            'workspaces' => 'live',
            'path' => '/module/{extension.vendorName}/{backendModule.key}',
            'labels' => 'LLL:EXT:{extension.extensionKey}/Resources/Private/Language/locallang_{backendModule.key}.xlf',
            'extensionName' => '{extension.extensionName}',
            'controllerActions' => [
                ModuleController::class => [
                    'flash','tree','clipboard','links','fileReference','fileReferenceCreate',
                ],
            ],
            'routes' => [
                '_default' => [
                    'target' => 'test',
                ],
            ],
        ],
    </f:for>
];
