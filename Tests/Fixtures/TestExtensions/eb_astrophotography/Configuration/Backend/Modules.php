<?php
return [
    'eb_astrophotography_astromanager' => [
        'parent' => 'web',
        'position' => [],
        'access' => 'user,group',
        'iconIdentifier' => 'eb_astrophotography-module-astromanager',
        'labels' => 'LLL:EXT:eb_astrophotography/Resources/Private/Language/locallang_astromanager.xlf',
        'extensionName' => 'EbAstrophotography',
        'controllerActions' => [
            \AcmeCorp\EbAstrophotography\Controller\AstroImageController::class => ['list', 'show'],
            
        ],
    ],
];
