<?php
defined('TYPO3_MODE') || die();

call_user_func(static function() {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'TestExtension',
        'web',
        'testmodule1',
        '',
        [
            \FIXTURE\TestExtension\Controller\MainController::class => 'list, show, new, create, edit, update, delete',
        ],
        [
            'access' => 'user,group',
            'icon'   => 'EXT:test_extension/Resources/Public/Icons/user_mod_testmodule1.svg',
            'labels' => 'LLL:EXT:test_extension/Resources/Private/Language/locallang_testmodule1.xlf',
        ]
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_testextension_domain_model_main', 'EXT:test_extension/Resources/Private/Language/locallang_csh_tx_testextension_domain_model_main.xlf');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_testextension_domain_model_main');

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_testextension_domain_model_child1', 'EXT:test_extension/Resources/Private/Language/locallang_csh_tx_testextension_domain_model_child1.xlf');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_testextension_domain_model_child1');

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_testextension_domain_model_child2', 'EXT:test_extension/Resources/Private/Language/locallang_csh_tx_testextension_domain_model_child2.xlf');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_testextension_domain_model_child2');

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_testextension_domain_model_child3', 'EXT:test_extension/Resources/Private/Language/locallang_csh_tx_testextension_domain_model_child3.xlf');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_testextension_domain_model_child3');

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_testextension_domain_model_child4', 'EXT:test_extension/Resources/Private/Language/locallang_csh_tx_testextension_domain_model_child4.xlf');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_testextension_domain_model_child4');
});
