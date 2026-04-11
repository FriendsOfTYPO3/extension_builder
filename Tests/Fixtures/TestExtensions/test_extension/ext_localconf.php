<?php
defined('TYPO3') || die();

(static function() {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'TestExtension',
        'Testplugin',
        [
            \FIXTURE\TestExtension\Controller\MainController::class => 'list, show, new, create, edit, update, delete, custom'
        ],
        // non-cacheable actions
        [
            \FIXTURE\TestExtension\Controller\MainController::class => 'create, update, delete, '
        ],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );
})();
