<?php
return [
    'ExtensionBuilder::wiringEditorSmdEndpoint' => [
        'path' => '/extensionBuilder/wireEditor',
        'target' => EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager::class . '::getWiringEditorSmd'
    ],
];
