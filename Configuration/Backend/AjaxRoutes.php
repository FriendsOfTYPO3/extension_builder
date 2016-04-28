<?php
return [
    'ExtensionBuilder::wiringEditorSmdEndpoint' => [
        'path' => '/extensionBuilder/wireEditor',
        'target' => EBT\ExtensionBuilder\Configuration\ConfigurationManager::class . '::getWiringEditorSmd'
    ],
];
