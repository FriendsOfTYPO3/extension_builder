<?php

use EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager;
use EBT\ExtensionBuilder\Controller\BuilderModuleController;

return [
    'eb_wiringEditorSmdEndpoint' => [
        'path' => '/extensionBuilder/wireEditor',
        'target' => ExtensionBuilderConfigurationManager::class . '::getWiringEditorSmd'
    ],
    'eb_dispatchRpcAction' => [
        'path' => '/extensionBuilder/dispatchRpcAction',
        'target' => BuilderModuleController::class . '::dispatchRpcAction'
    ],
    'eb_ajaxTesting' => [
        'path' => '/extensionBuilder/ajaxTesting',
        'target' => BuilderModuleController::class . '::ajaxTestingAction'
    ],
];