<?php
defined('TYPO3_MODE') || die('Access denied.');
call_user_func(
    function()
    {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'FIXTURE.TestExtension',
            'Testplugin',
            [
                'Main' => 'list, show, new, create, edit, update, delete'
            ],
            // non-cacheable actions
            [
                'Main' => 'create, update, delete'
            ]
        );

    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
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
        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);

            $iconRegistry->registerIcon(
                'test_extension-plugin-testplugin',
                \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
                ['source' => 'EXT:test_extension/Resources/Public/Icons/user_plugin_testplugin.svg']
            );
    }
);
