<?php
defined('TYPO3_MODE') || die();

if (TYPO3_MODE === 'BE') {
    /**
     * Register Backend Module
     */
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'ExtensionBuilder',
        'tools',
        'extensionbuilder',
        '',
        [
            \EBT\ExtensionBuilder\Controller\BuilderModuleController::class => 'index,domainmodelling,dispatchRpc',
        ],
        [
            'access' => 'user,group',
            'icon' => 'EXT:extension_builder/Resources/Public/Icons/Extension.svg',
            'labels' => 'LLL:EXT:extension_builder/Resources/Private/Language/locallang_mod.xlf',
        ]
    );
}
