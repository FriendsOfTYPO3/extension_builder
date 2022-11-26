<?php

use EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager;

return [
    'ExtensionBuilder::wiringEditorSmdEndpoint' => [
        'path' => '/extensionBuilder/wireEditor',
        'target' => ExtensionBuilderConfigurationManager::class . '::getWiringEditorSmd'
    ],
];
