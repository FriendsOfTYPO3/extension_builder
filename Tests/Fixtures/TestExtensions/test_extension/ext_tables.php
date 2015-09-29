<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'FIXTURE.' . $_EXTKEY,
	'Testplugin',
	'Test plugin'
);

if (TYPO3_MODE === 'BE') {

	/**
	 * Registers a Backend Module
	 */
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'FIXTURE.' . $_EXTKEY,
		'web',	 // Make module a submodule of 'web'
		'testmodule1',	// Submodule key
		'',						// Position
		array(
			'Main' => 'list, show, new, create, edit, update, delete',
		),
		array(
			'access' => 'user,group',
			'icon'   => 'EXT:' . $_EXTKEY . '/ext_icon.gif',
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_testmodule1.xlf',
		)
	);

}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'ExtensionBuilder Test Extension');

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
