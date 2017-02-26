<?php
defined('TYPO3_MODE') || die('Access denied.');
call_user_func(
    function($extKey)
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
						icon = ' . \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($extKey) . 'Resources/Public/Icons/user_plugin_testplugin.svg
						title = LLL:EXT:test_extension/Resources/Private/Language/locallang_db.xlf:tx_test_extension_domain_model_testplugin
						description = LLL:EXT:test_extension/Resources/Private/Language/locallang_db.xlf:tx_test_extension_domain_model_testplugin.description
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
    },
    $_EXTKEY
);
