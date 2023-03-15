<?php
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use FIXTURE\TestExtension\Controller\MainController;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
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

    // wizards
    ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    testplugin {
                        iconIdentifier = test_extension-plugin-testplugin
                        title = LLL:EXT:test_extension/Resources/Private/Language/locallang_db.xlf:tx_test_extension_testplugin.name
                        description = LLL:EXT:test_extension/Resources/Private/Language/locallang_db.xlf:tx_test_extension_testplugin.description
                        tt_content_defValues {
                            CType = list
                            list_type = testextension_testplugin
                        }
                    }
                }
                show = *
            }
       }'
    );
})();
