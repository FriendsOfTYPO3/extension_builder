<?php
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use FIXTURE\TestExtension\Controller\MainController;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
defined('TYPO3') || die();

(static function() {
    ExtensionUtility::registerModule(
        'TestExtension',
        'web',
        'testmodule1',
        '',
        [
            MainController::class => 'list, show, new, create, edit, update, delete, custom',
        ],
        [
            'access' => 'user,group',
            'icon'   => 'EXT:test_extension/Resources/Public/Icons/user_mod_testmodule1.svg',
            'labels' => 'LLL:EXT:test_extension/Resources/Private/Language/locallang_testmodule1.xlf',
        ]
    );

    ExtensionManagementUtility::addLLrefForTCAdescr('tx_testextension_domain_model_main', 'EXT:test_extension/Resources/Private/Language/locallang_csh_tx_testextension_domain_model_main.xlf');
    ExtensionManagementUtility::allowTableOnStandardPages('tx_testextension_domain_model_main');

    ExtensionManagementUtility::addLLrefForTCAdescr('tx_testextension_domain_model_child1', 'EXT:test_extension/Resources/Private/Language/locallang_csh_tx_testextension_domain_model_child1.xlf');
    ExtensionManagementUtility::allowTableOnStandardPages('tx_testextension_domain_model_child1');

    ExtensionManagementUtility::addLLrefForTCAdescr('tx_testextension_domain_model_child2', 'EXT:test_extension/Resources/Private/Language/locallang_csh_tx_testextension_domain_model_child2.xlf');
    ExtensionManagementUtility::allowTableOnStandardPages('tx_testextension_domain_model_child2');

    ExtensionManagementUtility::addLLrefForTCAdescr('tx_testextension_domain_model_child3', 'EXT:test_extension/Resources/Private/Language/locallang_csh_tx_testextension_domain_model_child3.xlf');
    ExtensionManagementUtility::allowTableOnStandardPages('tx_testextension_domain_model_child3');

    ExtensionManagementUtility::addLLrefForTCAdescr('tx_testextension_domain_model_child4', 'EXT:test_extension/Resources/Private/Language/locallang_csh_tx_testextension_domain_model_child4.xlf');
    ExtensionManagementUtility::allowTableOnStandardPages('tx_testextension_domain_model_child4');

    ExtensionManagementUtility::addLLrefForTCAdescr('tx_testextension_domain_model_category', 'EXT:test_extension/Resources/Private/Language/locallang_csh_tx_testextension_domain_model_category.xlf');
    ExtensionManagementUtility::allowTableOnStandardPages('tx_testextension_domain_model_category');
})();
