<?php
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use FIXTURE\TestExtension\Controller\MainController;
defined('TYPO3') || die();

(static function() {
    ExtensionUtility::configurePlugin(
        'TestExtension',
        'Testplugin',
        [
            MainController::class => 'list, show, new, create, edit, update, delete, custom'
        ],
        // non-cacheable actions
        [
            MainController::class => 'create, update, delete, '
        ]
    );
})();
